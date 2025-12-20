<?php

namespace gamegam\WorldGuardPlugin;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Data\GuarddFullData;
use pocketmine\entity\object\FallingBlock;
use pocketmine\Server;

class Task extends \pocketmine\scheduler\Task{

	public function onRun(): void
	{
		foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
			foreach ($world->getEntities() as $entity) {
				if ($entity instanceof FallingBlock) {
					$pos = $entity->getPosition();
					$guardData = GuarddData::getInstance();
					$guard = WorldData::getInstance();
					if ($guard->getBlockJoin($pos)) {
						if ($guardData->getFlag($guard->getName($pos), "falling-blocks")) {
							$world->setBlock($pos, $entity->getBlock());
							$entity->flagForDespawn();
						}
					}
                    $d = GuarddFullData::getInstance();
                    if ($d->getFlag($guard->getName($pos), "falling-blocks")){
                        $world->setBlock($pos, $entity->getBlock());
                        $entity->flagForDespawn();
                    }
				}
			}
		}
	}
}