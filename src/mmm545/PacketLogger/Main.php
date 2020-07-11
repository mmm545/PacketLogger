<?php

declare(strict_types=1);

namespace mmm545\PacketLogger;

use DateTime;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\utils\TextFormat as TF;
class Main extends PluginBase implements Listener{
       public function onEnable(){
           $this->saveDefaultConfig();
           $this->getServer()->getPluginManager()->registerEvents($this, $this);
           if(!file_exists($this->getDataFolder()."\packets.log")){
               file_put_contents($this->getDataFolder()."\packets.log", "#[Date Time] #PacketName\n");
           }
       }

       public function onSend(DataPacketSendEvent $event){
           //packets go brrrr
           if(!$this->getConfig()->get("log_sent_packets")){
               return false;
           }
           if($this->getConfig()->get("mode") == "blacklist") {
               $this->blacklist($event, "sent");
               return true;
           }
           if($this->getConfig()->get("mode") == "whitelist") {
               $this->whitelist($event, "sent");
               return true;
           }
       }

       public function onReceive(DataPacketReceiveEvent $event){
           //packets go brrrr
           if(!$this->getConfig()->get("log_received_packets")){
               return false;
           }
           if($this->getConfig()->get("mode") == "blacklist") {
               $this->blacklist($event, "received");
               return true;
           }
           if($this->getConfig()->get("mode") == "whitelist") {
               $this->whitelist($event, "received");
               return true;
           }
       }
       public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
       {
           switch($command->getName()){
               case "pklog":
               if(!file_exists($this->getDataFolder()."\packets.log")){
                   $sender->sendMessage(TF::RED."Log file doesn't exist, creating new one");
                   file_put_contents($this->getDataFolder()."\packets.log", "#[Date Time] #PacketName\n");
                   return false;
                   //lol rip
               }
               $log = file_get_contents($this->getDataFolder()."\packets.log");
               if($log !== false){
                   $sender->sendMessage($log);
               }
               else{
                   $sender->sendMessage(TF::RED."Error: can't get file contents");
                   //lol rip
               }
               break;
               case "pkclear":
               if(!file_exists($this->getDataFolder()."\packets.log")){
                   $sender->sendMessage(TF::RED."Log file doesn't exist, creating new one");
                   file_put_contents($this->getDataFolder()."\packets.log", "#[Date Time] #PacketName\n");
                   return false;
               }
               //Log: *fades away*
               file_put_contents($this->getDataFolder()."\packets.log", "#[Date Time] #PacketName\n");
               $sender->sendMessage("Log file has been cleared");
               break;
               case "pkreload":
               $this->getConfig()->reload();
               $sender->sendMessage("Config has been successfully reloaded");
               break;
           }
           return true;
       }
       private function blacklist($event, string $sentOrReceived){
           $pkname = $event->getPacket()->getName();
           $date = new DateTime();
           if(!in_array($pkname, $this->getConfig()->get("packets"))){
               $msg = "[".$date->format('Y-m-d H:i:s:v')."] ".$pkname;
               $file = $this->getDataFolder() ."\packets.log";
               file_put_contents($file, $msg." has been ".$sentOrReceived."!\n", FILE_APPEND | LOCK_EX);
           }
       }
       private function whitelist($event, $sentOrReceived){
           $pkname = $event->getPacket()->getName();
           $date = new DateTime();
           if(in_array($pkname, $this->getConfig()->get("packets"))){
               $msg = "[".$date->format('Y-m-d H:i:s:v')."] ".$pkname;
               $file = $this->getDataFolder() ."\packets.log";
               file_put_contents($file, $msg." has been ".$sentOrReceived."!\n", FILE_APPEND | LOCK_EX);
           }
       }
}