<?php

namespace gamegam\WorldGuardPlugin;

use pocketmine\utils\SingletonTrait;

class FlagAPI{
	
	use SingletonTrait;
	private Type $type;
	
	public function __construct()
	{
		self::setInstance($this);
		$this->type = new Type();
	}
	
	public function addFlag(string $flag)
	{
		$this->type->addFlag($flag);
	}

	public function deleteFlag(string $flag)
	{
		$this->type->deleteFlag($flag);
	}

	public function getFlag(): array
	{
		return $this->type->getArray();
	}

	public function isType($name): bool{
		$bool = false;
		if(isset($this->getFlag()[$name])){
			$bool = true;
		}
		return $bool;
	}

	public function getCount(): int
	{
		return count($this->getFlag());
	}

	public function remove_name()
	{
		return $this->type->remove_name();
	}
}