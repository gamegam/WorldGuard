<?php

namespace gamegam\WorldGuardPlugin\Form\Member;

use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MemberList implements Form{

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
			"content" => $this->lang->getString("form_member_list"),
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		if ($data === null)return;
		foreach ($this->list as $list){
			$region = $list[$data] ?? null;
		}
		$worlddata = WorldData::getInstance();
		$worldguard = WorldGuard::getInstance();

		$is = str_replace("(name)", $region, $this->lang->getString("notname"));
		if (!$worlddata->isName($region)) {
			$p->sendMessage($worldguard->getTag() . $is);
			return;
		}
		$member = [];
		foreach ($this->api->db["name"][$region]["member"] as $list => $bool) {
			$member[] = $list;
		}
		$p->sendForm(new MemberForm($this->api, [$member], $region));
	}
}