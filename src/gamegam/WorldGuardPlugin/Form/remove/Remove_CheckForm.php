<?php
namespace gamegam\WorldGuardPlugin\Form\remove;

use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class Remove_CheckForm implements Form
{

	public Main $api;
	public string $region = "";
	public $lang;
	public array $list = [];

	public function __construct(Main $main, string $region = "", array $list = [])
	{
		$this->api = $main;
		$this->region = $region;
		$this->lang = $this->api->getAPI();
		$this->list = $list;
	}
	public function jsonSerialize(): array
	{
		$msg = str_replace("(name)", $this->region, $this->lang->getString("form_remove_check"));
		return [
			"type" => "modal",
			"title" => $this->lang->getString("form_title"),
			"content" => $msg,
			"button1" => $this->lang->getString("form_remove_button1"),
			"button2" => $this->lang->getString("form_remove_button2")
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		$list = [];

		if (!isset($data))return;
		if ($data){
			$is = str_replace("(name)", $this->region, $this->api->getAPI()->getString("notname"));
			if (!$worlddata->isName($this->region)) {
				$p->sendMessage($worldguard->getTag() . $is);
				return;
			}
			$str = str_replace("(worldguard)", $this->region, $this->api->getAPI()->getString("removeguard"));
			$worlddata->removeGuard($this->region);
			$p->sendMessage($worldguard->getTag() . $str);
			$datalist = $this->api->db["name"] ?? null;
			if ($datalist !== null) {
				foreach ($datalist as $name => $data) {
					$list[] = $name;
				}
			}
			$p->sendForm(new RemoveList($this->api, [$list]));
		}else{
			$p->sendForm(new RemoveList($this->api, $this->list));
		}
	}
}