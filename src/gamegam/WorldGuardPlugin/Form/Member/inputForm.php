<?php

namespace gamegam\WorldGuardPlugin\Form\Member;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class inputForm implements Form	{

	private Main $api;
	private string $region;
	private  $lang;

	public function __construct(Main $main, string $region)
	{
		$this->api = $main;
		$this->region = $region;
		$this->lang = $this->api->getAPI();
	}

	public function jsonSerialize(): array
	{
		$str = str_replace("(name)", $this->region, $this->lang->getString("form_member_input"));
		return [
			"type" => "custom_form",
			"title" => $this->lang->getString("form_title"),
			"content" => [[
				"type" => "input",
				"text" => $str
			]]
		];
	}
	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		$gd = GuarddData::getInstance();
		if (!isset($data[0]))return;
		$pp = strtolower($data[0]);
		$is = str_replace("(name)", $this->region, $this->lang->getString("notname"));
		if (!$worlddata->isName($this->region)) {
			$p->sendMessage($worldguard->getTag() . $is);
			return;
		}
		if ($gd->getMembers($this->region, $pp)) {
			$p->sendMessage($worldguard->getTag() . $this->lang->getString("ism"));
			return;
		}
		$str = str_replace("(pp)", $pp, $this->lang->getString("addm"));
		$str = str_replace("(worldguard)", $this->region, $str);
		$worlddata->addMember($this->region, $pp);
		$p->sendMessage($worldguard->getTag() . $str);
	}
}