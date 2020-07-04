<?php

declare(strict_types=1);

namespace mmm545\PacketLogger;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;
class Main extends PluginBase implements Listener{
    public static $config;
       public function onEnable(){
           $this->saveDefaultConfig();
           $this->getServer()->getPluginManager()->registerEvents($this, $this);
           self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
           if(!file_exists($this->getDataFolder()."\packets.log")){
               file_put_contents($this->getDataFolder()."\packets.log", "#[HH:MM:SS] #PacketName\n");
           }
       }

       public function onSend(DataPacketSendEvent $event){
           //packets go brrrr
           if(!self::$config->get("log_sent_packets")){
               return false;
           }
           $pkname = $event->getPacket()->getName();
           if(self::$config->get("block_packets")){
               if(!in_array($pkname, self::$config->get("blocked_packets"))){
                   $msg = "[".date('H:i:s')."] ".$pkname;
                   $file = $this->getDataFolder()."\packets.log";
                   file_put_contents($file, $msg." has been sent!\n", FILE_APPEND | LOCK_EX);
               }
               return true;
           }
           $msg = "[".date('H:i:s') ."] ".$pkname;
           $file = $this->getDataFolder()."\packets.log";
           file_put_contents($file, $msg." has been sent!\n", FILE_APPEND | LOCK_EX);
       }

       public function onReceive(DataPacketReceiveEvent $event){
           //packets go brrrr
           if(!self::$config->get("log_received_packets")){
               return false;
           }
           $pkname = $event->getPacket()->getName();
           if(self::$config->get("block_packets")){
               if(!in_array($pkname, self::$config->get("blocked_packets"))){
                   $msg = "[".date('H:i:s')."] ".$pkname;
                   $file = $this->getDataFolder() ."\packets.log";
                   file_put_contents($file, $msg." has been received!\n", FILE_APPEND | LOCK_EX);
               }
               return true;
           }
           $msg = "[".date('H:i:s')."] ".$pkname;
           $file = $this->getDataFolder()."\packets.log";
           file_put_contents($file, $msg." has been received!\n", FILE_APPEND | LOCK_EX);
       }
       public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
       {
           switch($command->getName()){
               case "pklog":
               if(!file_exists($this->getDataFolder()."/packets.log")){
                   $sender->sendMessage(TF::RED."Log file doesn't exist");
                   return false;
                   //lol rip
               }
               $log = file_get_contents($this->getDataFolder()."/packets.log");
               if($log !== false){
                   $sender->sendMessage($log);
               }
               else{
                   $sender->sendMessage(TF::RED."Error: can't get file contents");
                   //lol rip
               }
               break;
               case "pkclear":
               if(!file_exists($this->getDataFolder()."/packets.log")){
                   $sender->sendMessage(TF::RED."Log file doesn't exist");
                   return false;
               }
               //Log: *fades away*
               file_put_contents($this->getDataFolder()."/packets.log", "#[HH:MM:SS] #PacketName\n");
               $sender->sendMessage("Log file has been cleared");
               break;
               case "pkreload":
               self::$config->reload();
               $sender->sendMessage("Config has been successfully reloaded");
               break;
           }
           return true;
       }
}