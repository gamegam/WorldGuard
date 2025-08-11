<?php
namespace gamegam\WorldGuardPlugin\Form\remove;

use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class RemoveList implements Form
{
	public array $list = [];
	public Main $api;
	public $lang;

	public function __construct(Main $main, array $list = [])
	{
		$this->api = $main;
		$this->list = $list;
		$this->lang = $this->api->getAPI();
	}

	public function jsonSerialize(): array
	{
		$list = [];
		foreach ($this->list as $name){
		}
		foreach ($name as $a){
			$list[] = ["text" => $a];
		}
		return [
			"type" => "form",
			"title" => $this->lang->getString("form_title"),
			"content" => $this->lang->getString("form_remove_content"),
			"buttons" => $list
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();

		if ($data === null)return;
		foreach ($this->list as $array){
			$region = $array[$data];
		}
		$is = str_replace("(name)", $region, $this->api->getAPI()->getString("notname"));
		if (!$worlddata->isName($region)) {
			$p->sendMessage($worldguard->getTag() . $is);
			return;
		}
		$p->sendForm(new Remove_CheckForm($this->api, $region, $this->list));
	}
}