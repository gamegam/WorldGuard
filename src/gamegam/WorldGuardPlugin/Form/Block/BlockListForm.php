<?php

namespace gamegam\WorldGuardPlugin\Form\Block;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\form\Form;
use pocketmine\player\Player;

class BlockListForm implements Form{

	private array $block;
	private Main $api;
	private $lang, $region;

	public function __construct(Main $main, array $block, $region)
	{
		$this->api = $main;
		$this->block = $block;
		$this->lang = $this->api->getAPI();
		$this->region = $region;
	}

	public function jsonSerialize(): array
	{
		$buttons = [];
		foreach ($this->block as $list){
		}
		foreach ($list as $name){
			$buttons[] = ["text" => $name];
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
		$worlddata = WorldData::getInstance();
		$worldguard = WorldGuard::getInstance();
		$blockdata = DataBlock::getInstance();
		if ($data === null)return;
		foreach ($this->block as $list){
			$block = $list[$data] ?? null;
		}
		$is = str_replace("(name)", $this->region, $this->lang->getString("notname"));
		if (!$worlddata->isName($this->region)) {
			$p->sendMessage($worldguard->getTag() . $is);
		}else{
			$msg = str_replace("(name)", $this->region, $this->lang->getString("allblock_add"));
			$msg = str_replace("(block)", $block, $msg);
			if (! $blockdata->setData_Block($this->region, $block)){
				$p->sendMessage($worldguard->getTag(). $msg);
			}else{
				// 제거
				$msg = str_replace("(name)", $this->region, $this->lang->getString("allblock_remove"));
				$msg = str_replace("(block)", $block, $msg);
				if ($blockdata->Remove_Data($this->region, $block)){
					// 삭제 메시지
					$p->sendMessage($worldguard->getTag(). $msg);
				}else{
					$msg = str_replace("(name)", $this->region, $this->lang->getString("allblock_removeno"));
					$msg = str_replace("(block)", $block, $msg);
					$p->sendMessage($msg);
				}
			}
		}
	}
}