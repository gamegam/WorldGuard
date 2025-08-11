<?php

namespace gamegam\WorldGuardPlugin;

use pocketmine\Server;

class Type{

	private array $default = [
		"build" => "build",
		"tnt" => "tnt",
		"invincible" => "invincible",
		"lava-flow" => "lava-flow",
		"use" => "use",
		"water" => "water",
		"tnt-damage" => "tnt-damage",
		"pvp" => "pvp",
		"mob-damage" => "mob-damage",
		"mob-pvp" => "mob-pvp",
		"fire" => "fire",
		"mob-spawn" => "mob-spawn",
		"chat" => "chat",
		"exit" => "exit",
		"item-drop" => "item-drop",
		"tp" => "tp",
		"fall-damage" => "fall-damage",
		"falling-blocks" => "falling-blocks"
	];

	private array $array;

	private int $count = 0;

	private bool $loag = false;

	public function __construct() {
		$this->array = $this->default;
	}

	private string $remove_name = "";

	public function addFlag(string $flag = "build")
	{
		$flag = strtolower($flag);
		if (!isset($this->array[$flag])) {
			if ($this->remove_name !== ""){
				throw new \RuntimeException("Flag Add Error: Do not add flags immediately after deleting them.");
			}
			if ($this->count > 22 && ! $this->loag) {
				Server::getInstance()->getLogger()->info("§cYou can add up to 23 flags.");
				$this->loag = true;
			}else{
				if ($this->loag){
				}else{
					$this->array[$flag] = $flag;
					$this->count ++;
					Server::getInstance()->getLogger()->info("§a{$flag} has been added.");
					$this->loag = false;
				}
			}
		}else{
			throw new \RuntimeException("{$flag} has already been added.");
		}
	}

	public function remove_name(): string
	{
		return $this->remove_name;
	}

	public function deleteFlag(string $flag = ""): bool
	{
		$removed = true;
		if ($flag !== ""){
			if (isset($this->default[$flag])){
			}else{
				if (isset($this->array[$flag])){
					$this->remove_name = "";
					$this->remove_name = $flag;
					unset($this->array[$flag]);
					$this->count --;
				}else{
					$removed = false;
				}
			}
		}
		return $removed;
	}

	public function getArray(): array
	{
		return $this->array;
	}

	public function isType($name): bool{
		$bool = false;
		if(isset($this->array[$name])){
			$bool = true;
		}
		return $bool;
	}
}