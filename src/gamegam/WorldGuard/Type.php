<?php

namespace gamegam\WorldGuard;

class Type{

	public array $array = [
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
	];

	public function isType($name): bool{
		$bool = false;
		if(isset($this->array[$name])){
			$bool = true;
		}
		return $bool;
	}
}