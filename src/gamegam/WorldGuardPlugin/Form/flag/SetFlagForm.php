<?php
namespace gamegam\WorldGuardPlugin\Form\flag;

use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\FlagAPI;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\Type;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SetFlagForm implements Form{

	private Main $api;
	private $lang;
	public string $region = "";

	public function __construct(Main $main, $region){
		$this->api = $main;
		$this->region = $region;
		$this->lang = $this->api->getAPI();
	}

	private array $ops = [
		0 => "deny",
		1 => "allow",
		2 => "none"
	];

	public function jsonSerialize(): array
	{
		$options = [];
		$type = FlagAPI::getInstance();
		foreach ($type->getFlag() as $array){
			$options[] = $array;
		}
		$str = str_replace("(name)", $this->region, $this->lang->getString("form_flag_set"));
		return [
			"type" => "custom_form",
			"title" => $this->lang->getString("form_title"),
			"content" => [[
				"type" => "label",
				"text" =>$str
			],
				[
					"type" => "dropdown",
					"text" => "",
					"options" => $options
				],
				[
					"type" => "dropdown",
					"text" => $this->lang->getString("form_flag_message"),
					"options" => [
						$this->lang->getString("form_flag_1"),
						$this->lang->getString("form_flag_2"),
						$this->lang->getString("form_flag_3")
					]
				]
			]
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		if (!isset($data[1]) || !isset($data[2]))return;
		$type = FlagAPI::getInstance();
		$values = array_values($type->getFlag());
		$options = $values[$data[1]] ?? null;
		if ($options === null)return;
		$as = $this->ops[$data[2]] ?? null;
		if ($as === null)return;
		$is = str_replace("(name)", $this->region, $this->lang->getString("notname"));
		if (!$worlddata->isName($this->region)) {
			$p->sendMessage($worldguard->getTag() . $is);
		}else{
			if (!$type->isType(strtolower($options))) {
				$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("noType"));
			}else{
				$f = str_replace("(flag)", $options, $this->api->getAPI()->getString("fadd"));
				$a = str_replace("(name)", $this->region, $f);
				$message = str_replace("(3)", $as, $a);
				$worlddata->WorldflagData($this->region, $options, $as);
				$p->sendMessage($message);
			}
		}
	}
}