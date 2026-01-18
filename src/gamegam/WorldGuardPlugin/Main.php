<?php

namespace gamegam\WorldGuardPlugin;

use gamegam\WorldGuardPlugin\command\worldFullCommand;
use gamegam\WorldGuardPlugin\command\WorldGuardCommand;
use gamegam\WorldGuardPlugin\EvnetListener\Blocks;
use gamegam\WorldGuardPlugin\EvnetListener\WorldGuardEvent\BlockGuard;
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
    public array $worlds = [];
	public $abc;

	public function onEnable() : void{
        self::setInstance($this);

		$path = Path::join($this->getDataFolder(), "worldGuard.json");
		if(file_exists($path)){
			$this->db = json_decode(Filesystem::fileGetContents($path), true);
		}

		$block = Path::join($this->getDataFolder(), "blocks.json");
		if (file_exists($block)){
			$this->block = json_decode(Filesystem::fileGetContents($block), true);
		}

        $worlds = Path::join($this->getDataFolder(), "worlds.json");
        if (file_exists($worlds)){
            $this->worlds = json_decode(Filesystem::fileGetContents($worlds), true);
        }

		$config = $this->getConfig();
		if (!$config->exists("warn_message")) {
			$config->set("warn_message", true);
			$config->save();
		}

		$this->abc = new LanguageFile($this);
		$this->abc->Create_File($this->getConfig()->get("language"));

		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
			new WorldGuardCommand($this),
            new worldFullCommand()
		]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerEvnet([
			$this,
			new Blocks($this),
			new BlockGuard($this),
			new Damage($this),
			new Player($this),
			new Entity($this)
			]
		);

		$this->getScheduler()->scheduleRepeatingTask(new Task(), 0);
	}

	public function onLoad(): void{
		self::setInstance($this);
	}

	public function isOP(string $name){
		return $this->getServer()->isOp($name);
	}

	public function registerEvnet(array $s){
		foreach($s as $list){
			$this->getServer()->getPluginManager()->registerEvents($list, $this);
		}
	}

	public function message(\pocketmine\player\Player $player, string $message){
		if ($this->getConfig()->get("warn_message") === true){
			$player->sendMessage(WorldGuard::getInstance()->getTag(). $message);
		}
	}

	public function getAPI(): LanguageFile{
		return $this->abc;
	}

	public function onDisable(): void
	{
		Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "worldGuard.json"), json_encode($this->db, JSON_UNESCAPED_UNICODE));
		Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "blocks.json"), json_encode($this->block, JSON_UNESCAPED_UNICODE));
        Filesystem::safeFilePutContents(Path::join($this->getDataFolder(), "worlds.json"), json_encode($this->worlds, JSON_UNESCAPED_UNICODE));
	}

	public function save():void{
		$this->onDisable();
	}
}