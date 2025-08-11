<?php

namespace gamegam\WorldGuardPlugin\event;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use pocketmine\entity\Location;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;

class RegionJoin extends Event{

    private Player $p;
    private string $region;
    private $guard;
    private PlayerEvent $event;
    private Main $api;

    public function __construct(Player $p, $region = "", GuarddData $guard, PlayerMoveEvent $event)
    {
        $this->p = $p;
        $this->region = $region;
        $this->guard = $guard;
        $this->event = $event;
        $this->api = Main::getInstance();
    }

    // 내 지역
    public function getRegion()
    {
        return $this->region;
    }

    public function getPlayer(): Player
    {
        return $this->p;
    }

    public function getData(): GuarddData
    {
        return $this->guard;
    }

    private function getMove(): PlayerMoveEvent
    {
        return $this->event;
    }

    public function cancel()
    {
        return $this->getMove()->cancel();
    }

    public function umcancel()
    {
        return $this->getMove()->uncancel();
    }

    public function getFrom()
    {
        return $this->getMove()->getFrom();
    }

    public function getTo()
    {
       return $this->getMove()->getTo();
    }
    public function isCancelled()
    {
        return $this->getMove()->isCancelled();
    }

    public function setTo(Location $location)
    {
        $this->getMove()->setTo($location);
    }

    public function getMember(): array
    {
        $member = [];
        foreach ($this->api->db["name"][$this->getRegion()]["member"] as $list => $bool) {
            $member[] = $list;
        }
        return $member;
    }

    public function addMember()
    {
        $worlddata = WorldData::getInstance();
        $worlddata->addMember($this->getRegion(), strtolower($this->p->getName()));
    }
}