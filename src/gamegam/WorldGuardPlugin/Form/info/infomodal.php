<?php

namespace gamegam\WorldGuardPlugin\Form\info;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\FlagAPI;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\Type;
use pocketmine\form\Form;
use pocketmine\player\Player;

class infomodal implements Form{

	private Main $api;
	private $region, $lang;

	public function __construct(Main $main, string $region)
	{
		$this->api = $main;
		$this->region = $region;
		$this->lang = $this->api->getAPI();
	}

	public function jsonSerialize(): array
	{
		$content = str_replace("(name)", $this->region, $this->lang->getString("form_info_content"));
		$type = FlagAPI::getInstance();
		$appliedFlags = $this->api->db["name"][$this->region]["flag"] ?? [];

		$flagArr = [];
		foreach ($type->getFlag() as $flagItem) {
			if (!empty($appliedFlags[$flagItem]) && $appliedFlags[$flagItem] === true) {
				$flagArr[] = "§a" . $flagItem . "§r";
			} else {
				$flagArr[] = "§c" . $flagItem . "§r";
			}
		}

		$flagText = implode(", ", $flagArr);
		$content = str_replace("(flag)", $flagText, $content);

		// Meber
		$member = [];
		foreach ($this->api->db["name"][$this->region]["member"] as $list => $bool) {
			$member[] = $list;
		}
		$member = implode(", ", $member);
		$content = str_replace("(member)", $member, $content);

		return [
			"type" => "modal",
			"title" => $this->lang->getString("form_title"),
			"content" => $content,
			"button1" => $this->lang->getString("form_info_button1"),
			"button2" => $this->lang->getString("form_info_button2")
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$list = [];
		$datalist = $this->api->db["name"] ?? null;
		if ($datalist !== null) {
			foreach ($datalist as $name => $d) {
				$list[] = $name;
			}
		}
		if (!isset($data))return;
		if (! $data){
			// Back Go
			$p->sendForm(new MainInfo($this->api, [$list]));
		}
	}
}