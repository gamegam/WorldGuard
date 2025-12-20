<?php

namespace gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Data\GuarddFullData;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\block\utils\Fallable;
use pocketmine\entity\object\FallingBlock;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class Entity implements Listener{

	public Main $api;
	public $tag;

	public function __construct(Main $api){
		$this->tag = WorldGuard::getInstance()->getTag();
		$this->api = $api;
	}

	public function MobSpawn(EntitySpawnEvent $ev){
		$pos = $ev->getEntity()->getPosition();
		$guardData = GuarddData::getInstance();
		$guard = WorldData::getInstance();
		if ($guard->getBlockJoin($pos)){
			if ($guardData->getMobSpawn($guard->getName($pos))){
				if (! $ev->getEntity() instanceof Player && ! $ev->getEntity() instanceof Painting && ! $ev->getEntity() instanceof  FallingBlock){
					$ev->getEntity()->flagForDespawn();
				}
			}
		}
        $da = GuarddFullData::getInstance();
        if ($da->getMobSpawn($pos->getWorld()->getFolderName())){
            $ev->getEntity()->flagForDespawn();
        }
	}

	public function onBlockUpdate(BlockUpdateEvent $ev): void
	{
		$block = $ev->getBlock();
		$guardData = GuarddData::getInstance();
		$guard = WorldData::getInstance();
		if ($block instanceof Fallable) {
			$pos = $block->getPosition();
			if ($guard->getBlockJoin($pos)) {
				if ($guardData->getFlag($guard->getName($pos), "falling-blocks")) {
					$ev->cancel();
				}
			}
            $da = GuarddFullData::getInstance();
            if ($da->getFlag($guard->getName($pos), "falling-blocks")) {
                $ev->cancel();
            }
		}
	}
}