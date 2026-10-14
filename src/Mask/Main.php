<?php

declare(strict_types=1);

namespace Mask;

use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use pocketmine\world\sound\XpCollectSound;

final class Main extends PluginBase implements Listener{
	private const MANAGED_MASK_TAG = "NhanAZMaskManaged";
	private const MANAGED_MASK_VALUE = "mask-v1";

	private Config $playerModes;
	private Config $helmetSnapshots;

	/** @var array<string, Item|null> */
	private array $snapshotCache = [];

	/** @var array<string, true> */
	private array $warnedInvalidSnapshots = [];

	protected function onEnable() : void{
		@mkdir($this->getDataFolder(), 0777, true);
		$this->playerModes = new Config($this->getDataFolder() . "NguoiChoi.yml", Config::YAML, []);
		$this->helmetSnapshots = new Config($this->getDataFolder() . "MaskData.yml", Config::YAML, []);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			$this->updateMasks();
		}), 10);
		$this->getLogger()->info("Đã Hoạt Động");
	}

	protected function onDisable() : void{
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->restoreForShutdown($player);
		}
		$this->playerModes->save();
		$this->helmetSnapshots->save();
		$this->getLogger()->info("Đã Vô Hiệu Hóa");
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		$name = $event->getPlayer()->getName();
		if(!$this->playerModes->exists($name)){
			$this->playerModes->set($name, "off");
			$this->playerModes->save();
		}
	}

	public function onQuit(PlayerQuitEvent $event) : void{
		$this->restoreForShutdown($event->getPlayer());
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(strtolower($command->getName()) !== "mask"){
			return false;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage("Vui lòng sử dụng lệnh này trong trò chơi.");
			return true;
		}
		if(count($args) !== 1 || !in_array(strtolower($args[0]), ["on", "off"], true)){
			$sender->sendMessage("Sử dụng: /mask <on|off>");
			return true;
		}

		if(strtolower($args[0]) === "on"){
			if(!$this->prepareSnapshot($sender)){
				$sender->sendMessage("Không thể bật mặt nạ an toàn vì dữ liệu mũ cũ không đọc được.");
				return true;
			}
			$this->playerModes->set($sender->getName(), "on");
			$this->playerModes->save();
			$this->applyMaskIfSafe($sender);
			$sender->sendMessage("§l§f[§bMask§f]§e Đã §aBật§e. Dùng §b/mask off§e để tắt.");
			return true;
		}

		if(!$this->turnOff($sender)){
			return true;
		}
		$sender->sendMessage("§l§f[§bMask§f]§e Đã §cTắt§e. Dùng §b/mask on§e để bật lại.");
		return true;
	}

	private function updateMasks() : void{
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$mode = $this->playerModes->get($player->getName(), "off");
			if(!is_string($mode) || strtolower($mode) !== "on"){
				continue;
			}
			if($this->prepareSnapshot($player)){
				$this->applyMaskIfSafe($player);
			}
		}
	}

	private function prepareSnapshot(Player $player) : bool{
		$key = $this->getPlayerKey($player);
		if($this->helmetSnapshots->exists($key)){
			return $this->getSnapshot($player) !== null;
		}

		$helmet = $player->getArmorInventory()->getHelmet();
		if($this->isManagedMask($helmet)){
			$this->warnSnapshotProblem($key, "A plugin-managed mask was found without its saved helmet; leaving it untouched.");
			return false;
		}

		$this->storeSnapshot($player, $helmet);
		return true;
	}

	private function applyMaskIfSafe(Player $player) : void{
		$key = $this->getPlayerKey($player);
		$hasSnapshot = $this->helmetSnapshots->exists($key);
		$snapshot = $hasSnapshot ? $this->getSnapshot($player) : null;
		if($hasSnapshot && $snapshot === null){
			return;
		}

		$helmet = $player->getArmorInventory()->getHelmet();
		if(!HelmetRestorePolicy::shouldApplyMask($helmet, $snapshot, $hasSnapshot, $this->isManagedMask($helmet))){
			return;
		}

		$player->getArmorInventory()->setHelmet($this->createMask());
	}

	private function turnOff(Player $player) : bool{
		$key = $this->getPlayerKey($player);
		if(!$this->helmetSnapshots->exists($key)){
			$this->playerModes->set($player->getName(), "off");
			$this->playerModes->save();
			return true;
		}

		$snapshot = $this->getSnapshot($player);
		if($snapshot === null){
			$player->sendMessage("Không thể tắt mặt nạ vì bản sao mũ đã lưu bị lỗi. Hãy giữ nguyên dữ liệu plugin để khôi phục thủ công.");
			return false;
		}

		$armor = $player->getArmorInventory();
		$helmet = $armor->getHelmet();
		$decision = HelmetRestorePolicy::decideOffAction(
			$helmet,
			$snapshot,
			$this->isManagedMask($helmet),
			$player->getInventory()->canAddItem($snapshot)
		);

		switch($decision){
			case HelmetRestoreDecision::RESTORE_HELMET:
				$armor->setHelmet(clone $snapshot);
				break;
			case HelmetRestoreDecision::RETURN_TO_INVENTORY:
				$leftovers = $player->getInventory()->addItem(clone $snapshot);
				if($leftovers !== []){
					$player->sendMessage("Kho đồ không còn chỗ trống. Mặt nạ vẫn bật để tránh làm mất mũ đã lưu.");
					return false;
				}
				break;
			case HelmetRestoreDecision::DISCARD_EMPTY_SNAPSHOT:
				break;
			case HelmetRestoreDecision::KEEP_ENABLED:
				$player->sendMessage("Bạn đã đổi mũ khi mặt nạ đang bật. Hãy cất mũ hiện tại vào kho đồ rồi chạy /mask off; mũ đã lưu vẫn được giữ an toàn.");
				return false;
		}

		// Persist the off state before dropping the backup. If interrupted here,
		// the remaining snapshot is harmless and can still be recovered manually.
		$this->playerModes->set($player->getName(), "off");
		$this->playerModes->save();
		$this->removeSnapshot($player);
		return true;
	}

	private function restoreForShutdown(Player $player) : void{
		$key = $this->getPlayerKey($player);
		if(!$this->helmetSnapshots->exists($key)){
			return;
		}

		$snapshot = $this->getSnapshot($player);
		if($snapshot === null){
			return;
		}

		$armor = $player->getArmorInventory();
		$helmet = $armor->getHelmet();
		if($this->isManagedMask($helmet) || $helmet->isNull() || $helmet->equals($snapshot)){
			$armor->setHelmet(clone $snapshot);
		}
	}

	private function storeSnapshot(Player $player, Item $item) : void{
		$key = $this->getPlayerKey($player);
		$this->helmetSnapshots->set($key, HelmetSnapshotCodec::encode($item));
		$this->helmetSnapshots->save();
		$this->snapshotCache[$key] = clone $item;
		unset($this->warnedInvalidSnapshots[$key]);
	}

	private function getSnapshot(Player $player) : ?Item{
		$key = $this->getPlayerKey($player);
		if(array_key_exists($key, $this->snapshotCache)){
			return $this->snapshotCache[$key] !== null ? clone $this->snapshotCache[$key] : null;
		}

		$encoded = $this->helmetSnapshots->get($key);
		if(!is_string($encoded)){
			$this->snapshotCache[$key] = null;
			$this->warnSnapshotProblem($key, "Saved helmet snapshot is not a string.");
			return null;
		}

		try{
			$item = HelmetSnapshotCodec::decode($encoded);
		}catch(\Throwable $e){
			$this->snapshotCache[$key] = null;
			$this->warnSnapshotProblem($key, "Saved helmet snapshot could not be decoded: " . $e->getMessage());
			return null;
		}

		$this->snapshotCache[$key] = clone $item;
		return $item;
	}

	private function removeSnapshot(Player $player) : void{
		$key = $this->getPlayerKey($player);
		$this->helmetSnapshots->remove($key);
		$this->helmetSnapshots->save();
		unset($this->snapshotCache[$key], $this->warnedInvalidSnapshots[$key]);
	}

	private function createMask() : Item{
		$types = [
			MobHeadType::SKELETON(),
			MobHeadType::WITHER_SKELETON(),
			MobHeadType::ZOMBIE(),
			MobHeadType::PLAYER(),
			MobHeadType::CREEPER(),
			MobHeadType::DRAGON()
		];
		$type = $types[random_int(0, count($types) - 1)];

		$item = VanillaBlocks::MOB_HEAD()->setMobHeadType($type)->asItem();
		$tag = $item->getNamedTag();
		$tag->setString(self::MANAGED_MASK_TAG, self::MANAGED_MASK_VALUE);
		return $item->setNamedTag($tag);
	}

	private function isManagedMask(Item $item) : bool{
		return $item->getNamedTag()->getString(self::MANAGED_MASK_TAG, "") === self::MANAGED_MASK_VALUE;
	}

	private function getPlayerKey(Player $player) : string{
		return $player->getUniqueId()->toString();
	}

	private function warnSnapshotProblem(string $key, string $message) : void{
		if(isset($this->warnedInvalidSnapshots[$key])){
			return;
		}
		$this->warnedInvalidSnapshots[$key] = true;
		$this->getLogger()->warning($message . " Player UUID: " . $key);
	}
}
