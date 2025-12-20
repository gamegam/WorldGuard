<?php

namespace gamegam\WorldGuardPlugin\Data;


use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\block\Block;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class GuarddFullData
{

	use SingletonTrait;

	public $api, $worldguard, $data;

	public function __construct()
	{
		self::setInstance($this);
		$this->api = Server::getInstance()->getPluginManager()->getPlugin("WorldGuardPlugin");
		$this->worldguard = WorldGuard::getInstance();
	}

    public function isName(string $name):bool{
        $bool = false;
        if (isset($this->api->worlds["name"][$name])){
            $bool = true;
        }
        return $bool;
    }

	public function getChat(string $name): bool
	{
		$bool = false;
		if ($this->isName($name)) {
			if (isset($this->api->world["name"][$name]["flag"]["chat"])) {
				$bool = true;
			}
		}
		return $bool;
	}

	public function getMobSpawn(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->world["name"][$name]["flag"]["mob-spawn"])){
				$bool = true;
			}
		}
		return $bool;
	}

	/**
	 * If you detect a build in the area
	 */

	public function getBuild(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["build"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getInteract(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["use"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getMembers(string $name, $p): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["member"][strtolower($p)])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getTNT(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["tnt"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getinvincible(string $name){
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["invincible"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getLave(string $name):bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["lava-flow"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getWater(string $name):bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["water"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getTNTDamage(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["tnt-damage"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getPVP(string $name){
		$bool = false;
		if ($this->isName($name)){
			if (isset($this->api->worlds["name"][$name]["flag"]["pvp"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getMobDamage(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["mob-damage"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getMobPVP(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["mob-pvp"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getfire(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["fire"])){
				$bool = true;
			}
		}
		return $bool;
	}

	// exit

	public function getExit(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["exit"])){
				$bool = true;
			}
		}
		return $bool;
	}
	// entry
	public function getEntry(string $name): bool
	{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["entry"])){
				$bool = true;
			}
		}
		return $bool;
	}

	// item drop
	public function getItemDrop(string $name): bool
	{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["item-drop"])){
				$bool = true;
			}
		}
		return $bool;
	}

	// tp
	public function getTP(string $name)
	{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["tp"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getFlag(string $name, $flag = "build"): bool
	{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"][$flag])){
				$bool = true;
			}
		}
		return $bool;
	}

    // insave
    public function getDeath(string $name): bool
    {
        $bool = false;
        if ($this->isName($name)){
            if(isset($this->api->worlds["name"][$name]["flag"]["keep-inventory"])){
                $bool = true;
            }
        }
        return $bool;
    }

	public function getFly(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["fly"])){
				$bool = true;
			}
		}
		return $bool;
	}

	public function getBow(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["bow"])){
				$bool = true;
			}
		}
		return $bool;
	}
	// end
	public function getPearl(string $name): bool{
		$bool = false;
		if ($this->isName($name)){
			if(isset($this->api->worlds["name"][$name]["flag"]["ender_pearl"])){
				$bool = true;
			}
		}
		return $bool;
	}
}