<?php
namespace gamegam\WorldGuardPlugin\Form;

use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class defineForm implements Form
{

	private Main $api;
	private $lang;
	private $input = "";

	public function __construct(Main $main, $input = "")
	{
		$this->api = $main;
		$this->lang = $this->api->getAPI();
		$this->input = $input;
	}

	public function jsonSerialize(): array
	{
		return [
			"type" => "custom_form",
			"title" => $this->lang->getString("form_title"),
			"content" => [[
				"type" => "input",
				"text" => $this->lang->getString("info_region"),
				"default" => $this->input
			]]
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		if (!isset($data[0]))return;
		$input = $data[0] ?? "";
		if (!$worldguard->isModel($p)) {
			$p->sendMessage($this->lang->getString("notMode"));
			return;
		}
		$is = str_replace("(name)", $input, $this->lang->getString("isname"));
		if ($worlddata->isName($input)) {
			$p->sendMessage($worldguard->getTag() . $is);
			$p->sendForm(new defineForm($this->api, $input));
		} else {
			$a = str_replace("(name)", $input, $this->lang->getString("create"));
			$worlddata->CreateGuard($p, $input);
			$p->sendMessage($worldguard->getTag() . $a);
		}
	}
}