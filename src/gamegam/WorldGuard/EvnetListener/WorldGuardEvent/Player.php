<?php

namespace gamegam\WorldGuard\EvnetListener\WorldGuardEvent;

use gamegam\WorldGuard\Data\GuarddData;
use gamegam\WorldGuard\Main;
use gamegam\WorldGuard\WorldData;
use gamegam\WorldGuard\WorldGuard;
use pocketmine\block\ItemFrame;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

class Player implements Listener{

	public function __construct(Main $api){
		$this->tag = WorldGuard::getInstance()->getTag();
		$this->api = $api;
	}

	public function onInteractEvent(PlayerInteractEvent $ev){
		$p = $ev->getPlayer();
		$block = $ev->getBlock();
		$data = WorldData::getInstance();
		$d = GuarddData::getInstance();
		$guard = WorldData::getInstance();
		$pos = $p->getPosition();
		$name = $data->getName($pos);
		$ac = $ev->getAction();
		if(! $p->hasPermission("worldGuard.permissimon")){
			if($guard->getBlockJoin($pos)){
				if ($d->getInteract($name)){
					if ($ac == 0 && ! $block instanceof ItemFrame){
						return true;
					}
					if(! $d->getMembers($name, $p->getName())){
						$p->sendMessage($this->tag. $this->api->getAPI()->getString("Use"));;
						$ev->cancel();
					}
				}
			}
		}
	}

	public function onMove(PlayerMoveEvent $ev){
		$p = $ev->getPlayer();
		$pos = $p->getPosition();
		$data = WorldData::getInstance();
		$d = GuarddData::getInstance();
		$guard = WorldData::getInstance();
		$name = $data->getName($pos);
		if($guard->getBlockJoin($pos)){
			if ($d->getfire($name)){
				if ($d->getfire($name)){
					$p->extinguish();
				}
			}
		}
	}
}