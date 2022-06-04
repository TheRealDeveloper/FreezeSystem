<?php

namespace trd\freeze;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;


class Main extends PluginBase implements Listener
{

    public $freezed = [];
    public $config;

    protected function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    }

    //--FreezeCommand--//
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() == "freeze"){
            if(isset($args[0])){
                $target = $this->getServer()->getPlayerByPrefix($args[0]);
                if($target instanceof Player){
                    $pmsg = str_replace("{NAME}", $target->getName(), $this->config->get("freezed.sender.message"));
                    $sender->sendMessage($pmsg);
                    $this->freezed[$target->getName()] = 1;
                }else {
                    $sender->sendMessage($this->config->get("is.not.player"));
                }
            }else{
              $sender->sendMessage($this->config->get("is.not.player"));
            }
        }
        if($command->getName() == "unfreeze"){
            if(isset($args[0])){
                $target = $this->getServer()->getPlayerByPrefix($args[0]);
                if($target instanceof Player){
                    $pmsg = str_replace("{NAME}", $target->getName(), $this->config->get("unfreezed.sender.message"));
                    $sender->sendMessage($pmsg);
                    unset($this->freezed[$target->getName()]);
                }else {
                    $sender->sendMessage($this->config->get("is.not.player"));
                }
            }else{
                $sender->sendMessage($this->config->get("is.not.player"));
            }
        }
        return true;
    }

    //---FREEZE---//
    public function onMove(PlayerMoveEvent $e){
        $player = $e->getPlayer();

        //--If player is a Player--//
        if($player instanceof Player){
            if(isset($this->freezed[$player->getName()])){
                $player->sendMessage($this->config->get("you.are.freezed"));
                $e->cancel();
            }
        }
    }
}