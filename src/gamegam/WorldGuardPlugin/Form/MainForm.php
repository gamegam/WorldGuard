<?php

namespace gamegam\WorldGuardPlugin\Form;

use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\Form\Block\BlockList;
use gamegam\WorldGuardPlugin\Form\flag\flagList;
use gamegam\WorldGuardPlugin\Form\info\infomodal;
use gamegam\WorldGuardPlugin\Form\info\MainInfo;
use gamegam\WorldGuardPlugin\Form\Member\inputForm;
use gamegam\WorldGuardPlugin\Form\Member\MainMember;
use gamegam\WorldGuardPlugin\Form\Member\MemberList;
use gamegam\WorldGuardPlugin\Form\remove\RemoveList;
use gamegam\WorldGuardPlugin\Language\ConfigGuard;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MainForm implements Form
{
	private Main $api;
	private $lang;

	public function __construct(Main $main)
	{
		$this->api = $main;
		$this->lang = $this->api->getAPI();
	}

	public function jsonSerialize(): array
	{
		return [
			"type" => "form",
			"title" => $this->lang->getString("form_title"),
			"content" => $this->lang->getString("form_content"),
			"buttons" => [
				[
					"text" => $this->lang->getString("button_1")
				],
				[
					"text" => $this->lang->getString("button_2")
				],
				[
					"text" => $this->lang->getString("button_3")
				],
				[
					"text" => $this->lang->getString("button_4")
				],
				[
					"text" => $this->lang->getString("button_5")
				],
				[
					"text" => $this->lang->getString("button_6")
				],
				[
					"text" => $this->lang->getString("button_7")
				],
				[
					"text" => $this->lang->getString("button_8")
				]
			]
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$list = [];
		$datalist = $this->api->db["name"] ?? null;
		if ($datalist !== null) {
			foreach ($datalist as $name => $d) {
				$list[] = $name;
			}
		}
		if ($data === null) return;
		if ($data === 0) {
			if ($worldguard->isMode($p)) {
				$p->sendMessage($this->lang->getString(ConfigGuard::isMode));
			} else {
				$p->sendMessage($this->lang->getString(ConfigGuard::position));
				$worldguard->setMode($p);
			}
		}else if ($data === 1){
			$p->sendForm(new defineForm($this->api));
		}else if ($data === 2){
			$p->sendForm(new RemoveList($this->api, [$list]));
		}else if ($data === 3){
			// flag set
			$p->sendForm(new flagList($this->api, [$list]));
		}else if ($data === 4){
			$p->sendForm(new MainInfo($this->api, [$list]));
		}else if ($data === 5){
			$p->sendForm(new MainMember($this->api, [$list]));
		}else if ($data === 6){
			$p->sendForm(new MemberList($this->api, [$list]));
		}else if ($data === 7){
			$p->sendForm(new BlockList($this->api, [$list]));
		}
	}
}