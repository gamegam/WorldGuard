<?php

namespace gamegam\WorldGuardPlugin\Form\info;

use gamegam\WorldGuardPlugin\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MainInfo implements Form{

	private Main $api;
	private array $list = [];
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
		foreach ($this->list as $list){
		}
		foreach ($list as $a){
			$buttons[] = ["text" => $a];
		}
		return [
			"type" => "form",
			"title" => $this->lang->getString("form_title"),
			"content" => "",
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		if ($data === null)return;
		foreach ($this->list as $list){
			$region = $list[$data] ?? null;
		}
		$p->sendForm(new infomodal($this->api, $region));
		//$p->sendForm(new inputForm($this->api, $region));
	}
}