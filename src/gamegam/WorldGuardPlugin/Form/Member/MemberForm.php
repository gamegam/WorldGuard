<?php

namespace gamegam\WorldGuardPlugin\Form\Member;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MemberForm implements Form{

	private Main $api;
	private array $list = [];
	private $lang;
	public $region;

	public function __construct(Main $main, array $list, $region)
	{
		$this->api = $main;
		$this->list = $list;
		$this->lang = $this->api->getAPI();
		$this->region = $region;
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
			"content" => $this->lang->getString("form_member_content"),
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		$gd = GuarddData::getInstance();
		if ($data === null)return;
		foreach ($this->list as $list){
			$member = $list[$data] ?? null;
		}
		$is = str_replace("(name)", $this->region, $this->lang->getString("isname"));
		if (!$worlddata->isName($this->region)) {
			$p->sendMessage($worldguard->getTag() . $is);
			return;
		}
		if (!$gd->getMembers($this->region, $member)) {
			$p->sendMessage($worldguard->getTag() . $this->lang->getString("notremove"));
			return;
		}
		$worlddata->RemoveMember($this->region, $member);
		$str = str_replace("(pp)", $member, $this->lang->getString("remove"));
		$str = str_replace("(worldguard)", $this->region, $str);
		$str = str_replace("(name)", $p->getName(), $str);
		$p->sendMessage($worldguard->getTag() . $str);
	}
}