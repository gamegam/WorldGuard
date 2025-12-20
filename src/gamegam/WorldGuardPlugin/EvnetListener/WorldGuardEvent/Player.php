<?php

namespace gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Data\GuarddFullData;
use gamegam\WorldGuardPlugin\event\RegionJoin;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\block\ItemFrame;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\GameMode;
use pocketmine\Server;

class Player implements Listener{

	public $tag, $api;

	private array $player = [];

	public function __construct(Main $api){
		$this->tag = WorldGuard::getInstance()->getTag();
		$this->api = $api;
	}

	public function onChat(PlayerChatEvent $ev){
		$p = $ev->getPlayer();
		$pos = $p->getPosition();
		$guard = WorldData::getInstance();
		$worlddata = GuarddData::getInstance();
		$name = $guard->getName($pos);
		if ($guard->getBlockJoin($pos)){
			if ($worlddata->getChat($name)){
				if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					//$p->sendMessage($this->tag. $this->api->getAPI()->getString("chat"));
					Main::getInstance()->message($p, $this->api->getAPI()->getString("chat"));
					$ev->cancel();
				}
			}
		}
        $da = GuarddFullData::getInstance();
        if ($da->getBuild($p->getWorld()->getFolderName())){
            if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $ev->cancel();
            }
        }
	}

	public function onLaunch(ProjectileLaunchEvent $event): void
	{
		$projectile = $event->getEntity();
		$p = $projectile->getOwningEntity();

		if ($p instanceof \pocketmine\player\Player && $projectile instanceof EnderPearl) {
			$pos = $p->getPosition();
			$guard = WorldData::getInstance();
			$d = GuarddData::getInstance();
			$name = $guard->getName($pos);
			if ($guard->getBlockJoin($pos)){
				if ($d->getPearl($name)){
					Main::getInstance()->message($p, $this->api->getAPI()->getString("ender_pearl"));
					$event->cancel();
				}
			}
			$da = GuarddFullData::getInstance();
			if ($da->getPearl($p->getWorld()->getFolderName())){
				Main::getInstance()->message($p, $this->api->getAPI()->getString("ender_pearl"));
				$event->cancel();
			}
		}
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
		if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
			if($guard->getBlockJoin($pos)){
				if ($d->getInteract($name)){
					if ($ac == 0 && ! $block instanceof ItemFrame){
						return true;
					}
					if(! $d->getMembers($name, $p->getName())){
						Main::getInstance()->message($p, $this->api->getAPI()->getString("Use"));
						$ev->cancel();
					}
				}
			}
		}
        $da = GuarddFullData::getInstance();
        if ($da->getBuild($p->getWorld()->getFolderName())){
            if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $ev->cancel();
            }
        }
	}

	public function onMove(PlayerMoveEvent $ev)
	{
		$p = $ev->getPlayer();
		$pos = $p->getPosition();
		$data = WorldData::getInstance();
		$d = GuarddData::getInstance();
		$guard = WorldData::getInstance();
		$da = GuarddFullData::getInstance();
		$name = $data->getName($pos);
		$score_hub = Server::getInstance()->getPluginManager()->getPlugin("ScoreHud");
		if ($score_hub !== null) {
			$event = new PlayerTagsUpdateEvent($p, [
				new ScoreTag("worldguard.regin.name", $name)
			]);
			$event->call();
		}
		if ($guard->getBlockJoin($pos)) {
			$new = new RegionJoin($p, $d, $ev, $name);
			$new->call();
			if ($d->getfire($name)) {
				if ($d->getfire($name)) {
					$p->extinguish();
				}
			}
			if ($da->getfire($p->getWorld()->getFolderName())) {
				$p->extinguish();
			}
		}
		// exit
		if ($guard->getBlockJoin($pos)) {
			if ($d->getExit($name)) {
				if (!$p->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
					if (!$d->getMembers($name, $p->getName())) {
						$this->player[$p->getName()]["exit"] = $name;
					}
				}
			} else {
				unset($this->player[$p->getName()]["exit"]);
			}
		} else {
			if (isset($this->player[$p->getName()]["exit"])) {
				$ev->cancel();
				unset($this->player[$p->getName()]["exit"]);
			}
		}

		if ($guard->getBlockJoin($pos)) {
			// fly
			$gm = $p->getGamemode();
			if ($gm !== GameMode::CREATIVE && $gm !== GameMode::SPECTATOR) {
				if ($d->getFly($name)) {
					if ($p->isFlying()) {
						$p->setAllowFlight(false);
						$p->setFlying(false);
						Main::getInstance()->message($p, $this->api->getAPI()->getString("fly"));
					}
				}
			}
		}
		if ($da->getFly($p->getWorld()->getFolderName())) {
			$gm = $p->getGamemode();
			if ($gm !== GameMode::CREATIVE && $gm !== GameMode::SPECTATOR) {
				if ($p->isFlying()) {
					$p->setAllowFlight(false);
					$p->setFlying(false);
					Main::getInstance()->message($p, $this->api->getAPI()->getString("fly"));
				}
			}
		}
	}

	public function onShoot(EntityShootBowEvent $event): void {
		$entity = $event->getEntity();
		if ($entity instanceof \pocketmine\player\Player){
			$guard = WorldData::getInstance();
			$d = GuarddData::getInstance();
			if ($guard->getBlockJoin($entity->getPosition())){
				if ($d->getBow($guard->getName($entity->getPosition()))){
					Main::getInstance()->message($entity, $this->api->getAPI()->getString("bow"));
					$event->cancel();
				}
			}else{
				$da = GuarddFullData::getInstance();
				if ($da->getBow($entity->getWorld()->getFolderName())){
					Main::getInstance()->message($entity, $this->api->getAPI()->getString("bow"));
					$event->cancel();
				}
			}
		}
	}

	public function PlayerDropItemEvent(PlayerDropItemEvent $ev)
	{
		$p = $ev->getPlayer();
		$guard = WorldData::getInstance();
		$d = GuarddData::getInstance();
		$pos = $p->getPosition();
		$data = WorldData::getInstance();
		$name = $data->getName($pos);
		if ($guard->getBlockJoin($pos)){
			if ($d->getItemDrop($name)){
				if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					if(! $d->getMembers($name, $p->getName())){
						Main::getInstance()->message($p, $this->api->getAPI()->getString("itme-drop"));
						$ev->cancel();
					}
				}
			}
		}

        $da = GuarddFullData::getInstance();
        if ($da->getItemDrop($p->getWorld()->getFolderName())){
            if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $ev->cancel();
            }
        }
	}


	public function teleportEvent(EntityTeleportEvent $ev)
	{
		$p = $ev->getEntity();
		if ($p instanceof \pocketmine\player\Player){
			unset($this->player[$p->getName()]);
			$guard = WorldData::getInstance();
			$d = GuarddData::getInstance();
			$pos = $p->getPosition();
			$data = WorldData::getInstance();
			$name = $data->getName($pos);
			if ($guard->getBlockJoin($pos)){
				if ($d->getTP($name)){
					if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
						if(! $d->getMembers($name, $p->getName())){
							Main::getInstance()->message($p, $this->api->getAPI()->getString("tp"));
							$ev->cancel();
						}
					}
				}
			}
		}
        $da = GuarddFullData::getInstance();
        if ($da->getTP($p->getWorld()->getFolderName())){
            if(! $p->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $ev->cancel();
            }
		}
	}

    public function onDeath(PlayerDeathEvent $ev){
        $p = $ev->getPlayer();
        $pos = $p->getPosition();
        $guard = WorldData::getInstance();
        $d = GuarddData::getInstance();
        $name = $guard->getName($pos);
        if ($guard->getBlockJoin($pos)){
            if ($d->getDeath($name)){
                $ev->setKeepInventory(true);
            }
        }

        $da = GuarddFullData::getInstance();
        if ($da->getDeath($p->getWorld()->getFolderName())){
            $ev->setKeepInventory(true);
        }
    }
}