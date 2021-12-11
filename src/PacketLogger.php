<?php

declare(strict_types=1);

namespace mmm545\PacketLogger;

use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\network\mcpe\protocol\Packet;

class PacketLogger extends PluginBase implements Listener {

    //please leave suggestions on how to improve this mess because i haven't coded in months and my brain hurts

    private string $logPath;

    protected function onEnable(): void
    {
        $this->logPath = $this->getDataFolder() . "packets.log";
        if(!file_exists($this->logPath)) $this->createLogFile();
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onSend(DataPacketSendEvent $event){
        if(!$this->getConfig()->get("log_sent_packets")){
            return;
        }

        foreach($event->getTargets() as $target){
            $playerName = $target->getPlayer()?->getName() ?? "unknown";
            foreach($event->getPackets() as $packet){
                $this->log($packet,
                $event->isCancelled(),
                "Server",
                $this->getServer()->getIp(),
                $this->getServer()->getPort(),
                $playerName,
                $target->getIp(),
                $target->getPort());
            }
        }
    }

    public function onReceive(DataPacketReceiveEvent $event){
        if(!$this->getConfig()->get("log_received_packets")){
            return;
        }
        $origin = $event->getOrigin();
        $playerName = $origin->getPlayer()?->getName() ?? "unknown";

        $this->log($event->getPacket(),
        $event->isCancelled(),
        $playerName,
        $origin->getIp(),
        $origin->getPort(),
        "Server",
        $this->getServer()->getIp(),
        $this->getServer()->getPort());
    }

    private function log(Packet $packet, $isCancelled, $src, $srcIp, $srcPort, $dst, $dstIp, $dstPort){
        $packetName = $packet->getName();
        $isCancelled = $isCancelled ? "true" : "false"; //any better way to do this? (in case this gets reviewed please give suggestions!)
        $blacklist = array_fill_keys($this->getConfig()->get("blacklisted_packets", []), true);
        $whitelist = array_fill_keys($this->getConfig()->get("whitelisted_packets", []), true);

        if($this->getConfig()->get("whitelist") && !isset($whitelist[$packetName])) return;
        if(isset($blacklist[$packetName])) return;

        $dateTime = (new DateTime())->format("Y-m-d H:i:s.v");
        $message = "$dateTime $packetName $isCancelled $src $srcIp $srcPort $dst $dstIp $dstPort\n";

        file_put_contents($this->logPath, $message, FILE_APPEND);

    }

    private function createLogFile(){
        file_put_contents($this->logPath, "#date time packet cancelled src src-ip src-port dst dst-ip dst-port\n");
    }

}
