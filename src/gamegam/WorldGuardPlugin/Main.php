<?php

namespace gamegam\WorldGuardPlugin;

use gamegam\WorldGuardPlugin\command\WorldGuardCommand;
use gamegam\WorldGuardPlugin\EvnetListener\Blocks;
use gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent\BlocGuard;
use gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent\Damage;
use gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent\Entity;
use gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent\Player;
use gamegam\WorldGuardPlugin\Language\LanguageFile;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase implements Listener{

    use SingletonTrait;

	public array $db = [];
	public array $block = [];
	public $abc;

	public function onEnable() : void{
        self::setInstance($this);

		$path = Path::join($this->getDataFolder(), "worldGuard.json");
		if(file_exists($path)){
			$this->db = json_decode(Filesystem::fileGetContents($path), true);
		}

		$block = Path::join($this->getDataFolder(), "blocks.json"); // 파괴 가능한 월드에서 해당 블럭 체크 (블럭 이름)
		if (file_exists($block)){
			$this->block = json_decode(Filesystem::fileGetContents($block), true);
		}

		$this->abc = new LanguageFile($this);
		$this->abc->Create_File($this->getConfig()->get("language"), $this->getConfig()->get("Developer_mode"));
		if ($this->getConfig()->get("Developer_mode")){
			$this->getServer()->getLogger()->info("§cEnable World Guard Developer Mode");
		}

		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
			new WorldGuardCommand($this)
		]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerEvnet([
			$this,
			new Blocks($this),
			new BlocGuard($this),
			new Damage($this),
			new Player($this),
			new Entity($this)
			]
		);

		$this->getScheduler()->scheduleRepeatingTask(new Task(), 0);
	}

	public function registerEvnet(array $s){
		foreach($s as $list){
			$this->getServer()->getPluginManager()->registerEvents($list, $this);
		}
	}

	public function getAPI(): LanguageFile{
		return $this->abc;
	}

	public function onDisable(): void
	{
		Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "worldGuard.json"), json_encode($this->db, JSON_UNESCAPED_UNICODE));
		Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "blocks.json"), json_encode($this->block, JSON_UNESCAPED_UNICODE));
	}

	public function save():void{
		$this->onDisable();
	}
}