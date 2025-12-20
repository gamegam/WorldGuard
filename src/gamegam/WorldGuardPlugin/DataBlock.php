<?php

namespace gamegam\WorldGuardPlugin;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class DataBlock
{

	use SingletonTrait;

	public $api;

	public function __construct()
	{
		self::setInstance($this);
		$this->api = Server::getInstance()->getPluginManager()->getPlugin("WorldGuardPlugin");
	}

	// 파괴 가능한 블럭 목록들
	public function getBlock(string $name): array
	{
		return $this->api->block[$name] ?? [];
	}

	public function isBlock(string $name, $block_name): bool
	{
		$bool = false;
		foreach ($this->getBlock($name) as $name) {
			if ($block_name == $name) {
				$bool = true;
				break;
			}
		}
		return $bool;
	}

	// setData
	public function setData_Block(string $name, $block_name): bool
	{
		$bool = false;
		if (!isset($this->api->block[$name][$block_name])) {
			$this->api->block[$name][$block_name] = $block_name;
		} else {
			$bool = true;
		}
		return $bool;
	}

	// remove Data
	public function Remove_Data(string $name, $block_name): bool
	{
		$bool = true;
		if (isset($this->api->block[$name][$block_name])) {
			unset($this->api->block[$name][$block_name]);
		} else {
			$bool = false; // 삭제가 불가능 할때
		}
		return $bool;
	}
}