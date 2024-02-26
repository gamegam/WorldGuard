<?php

namespace gamegam\WorldGuard;

use gamegam\WorldGuard\command\WorldGuardCommand;
use gamegam\WorldGuard\EvnetListener\Blocks;
use gamegam\WorldGuard\EvnetListener\WorldGuardEvent\Block;
use gamegam\WorldGuard\EvnetListener\WorldGuardEvent\Damage;
use gamegam\WorldGuard\EvnetListener\WorldGuardEvent\Player;
use gamegam\WorldGuard\GuardTask\Time;
use gamegam\WorldGuard\Language\ABC;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Filesystem;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase implements Listener{

	public array $db = [];

	public ABC $abc;

	public function onEnable() : void{
		$path = Path::join($this->getDataFolder(), "worldGuard.json");
		if(file_exists($path)){
			$this->db = json_decode(Filesystem::fileGetContents($path), true);
		}

		$this->abc = new ABC($this);
		$this->abc->Load($this->getConfig()->get("language"));

		$this->getServer()->getCommandMap()->registerAll("Main", [
			new WorldGuardCommand($this)
		]);

		$this->registerEvnet([
			new Blocks($this),
			$this,
			new Block($this),
			new Damage($this),
			new Player($this)
			]
		);
	}

	public function onDisable() : void{
		Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "worldGuard.json"), json_encode($this->db, JSON_UNESCAPED_UNICODE));
	}

	public function registerEvnet(array $s){
		foreach($s as $list){
			$this->getServer()->getPluginManager()->registerEvents($list, $this);
		}
	}

	public function getAPI(): ABC{
		return $this->abc;
	}

	public function save():void{
		$this->onDisable();
	}
}