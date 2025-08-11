<?php
namespace gamegam\WorldGuardPlugin\Form\Block;

use gamegam\WorldGuardPlugin\Form\flag\SetFlagForm;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class BlockList implements Form {

	public Main $api;

	private array $list;
	private $lang;

	public function __construct(Main $main, array $list)
	{
		$this->api = $main;
		$this->list = $list;
		$this->lang = $this->api->getAPI();
	}

	public function jsonSerialize(): array
	{
		$buttons = [];
		foreach ($this->list as $name){
		}
		foreach ($name as $a){
			$buttons[] = ["text" => $a];
		}
		return [
			"type" => "form",
			"title" => $this->lang->getString("form_title"),
			"content" => $this->lang->getString("form_flag_content"),
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();

		if ($data === null)return;
		foreach ($this->list as $name){
			$region = $name[$data]; // 선택한 지역
		}
		$is = str_replace("(name)", $region, $this->api->getAPI()->getString("notname"));
		if (!$worlddata->isName($region)) {
			$p->sendMessage($worldguard->getTag() . $is);
		}else{
			$block_list = $this->api->block[$region] ?? [];
			$list = [];
			foreach ($block_list as $La){
				$list[] = $La;
			}
			$p->sendForm(new BlockListForm($this->api, [$list], $region));
 		}
	}
}