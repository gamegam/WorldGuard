<?php

namespace gamegam\WorldGuardPlugin\command;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\FlagAPI;
use gamegam\WorldGuardPlugin\Form\MainForm;
use gamegam\WorldGuardPlugin\Language\ConfigGuard;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\block\Air;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;
use pocketmine\world\Position;

class WorldGuardCommand extends Command implements PluginOwned
{

	use PluginOwnedTrait;

	public Main $api;

	public function __construct(Main $api)
	{
		parent::__construct("rg", "worldGuard", null, [
			"regin"
		]);
		$this->api = $api;
		$this->setPermission("WorldGuardPlugin.permissimon");
	}

	public function flag(Player $p)
	{
		$i = implode(", ", FlagAPI::getInstance()->getFlag());
		$p->sendMessage($this->api->getAPI()->getString("flagList") . $i);
	}

	public function execute(CommandSender $p, string $commandLabel, array $args): void
	{
		$worldguard = WorldGuard::getInstance();
		$worlddata = WorldData::getInstance();
		$blockdata = DataBlock::getInstance();
		$gd = GuarddData::getInstance();
		$tag = $worldguard->getTag();
		if (!$p instanceof Player) {
			$p->sendMessage($tag . $this->api->getAPI()->getString(ConfigGuard::ingame));
		} else {
			if (!$this->testPermission($p)) {
				return;
			}
			if (!isset($args[0])) {
				$p->sendForm(new MainForm($this->api));
				$p->sendMessage($this->api->getAPI()->getString(ConfigGuard::HELP));
			} else {
				switch ($args[0]) {
					case "pos";
					case "position";
					if ($worldguard->isMode($p)) {
						$p->sendMessage($this->api->getAPI()->getString(ConfigGuard::isMode));
						return;
					}
					$p->sendMessage($this->api->getAPI()->getString(ConfigGuard::position));
					$worldguard->setMode($p);
					break;
					case "define";
					case "d";
						if (!$worldguard->isModel($p)) {
							$p->sendMessage($this->api->getAPI()->getString("notMode"));
							return;
						}
						if (!isset($args[1])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("noname"));
						} else {
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("isname"));
							if ($worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
							} else {
								$a = str_replace("(name)", $args[1], $this->api->getAPI()->getString("create"));
								$worlddata->CreateGuard($p, $args[1]);
								$p->sendMessage($worldguard->getTag() . $a);
							}
						}
						break;
					case "cancel";
						$worldguard->cancel($p);
						$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("cancel"));
						break;
					case "f";
					case "flag";
						if (isset($args[1]) && $args[1] == "list") {
							$this->flag($p);
							return;
						}
						if (!isset($args[1]) || !isset($args[2])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("f"));
						} else {
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("notname"));
							if (!$worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
								return;
							}
							$a = strtolower($args[2]);
							if (!isset($args[3])) {
								$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("f"));
								return;
							}
							$as = strtolower($args[3]);

							if ($as == "allow" || $as == "deny" || $as == "none") {
								$type = FlagAPI::getInstance();
								if (!$type->isType(strtolower($a))) {
									$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("noType"));
								} else {
									$f = str_replace("(flag)", $args[2], $this->api->getAPI()->getString("fadd"));
									$a = str_replace("(name)", $args[1], $f);
									$message = str_replace("(3)", $args[3], $a);
									$worlddata->WorldflagData($args[1], $args[2], $args[3]);
									$p->sendMessage($message);
								}
							} else {
								$p->sendMessage($this->api->getAPI()->getString("no3"));
							}
						}
						break;
					case "info";
						if (!isset($args[1])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("i"));
							return;
						}
						$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("isname"));
						if (!$worlddata->isName($args[1])) {
							$p->sendMessage($worldguard->getTag() . $is);
							return;
						}
						$flag = [];
						foreach ($this->api->db["name"][$args[1]]["flag"] as $list => $bool) {
							$flag[] = $list;
						}
						$member = [];
						foreach ($this->api->db["name"][$args[1]]["member"] as $list => $bool) {
							$member[] = $list;
						}
						$position = $this->api->db["name"][$args[1]];
						$flag = implode(", ", $flag);
						$member = implode(", ", $member);
						if ($member == "") {
							$member = $this->api->getAPI()->getString("noMenvers");
						} else {
							$member = $this->api->getAPI()->getString("playerlist") . "{$member}";
						}
						$pos1 = $position["pos1"];
						$pos2 = $position["pos2"];
						$e1 = explode(":", $pos1);
						$x = $e1[0] ?? 0;
						$y = $e1[1] ?? 0;
						$z = $e1[2] ?? 0;
						$all = $x . "," . $y . "," . $z;
						$e2 = explode(":", $pos2);
						$x = $e2[0] ?? 0;
						$y = $e2[1] ?? 0;
						$z = $e2[2] ?? 0;
						$world = $e1[3] ?? "";
						$all1 = $x . "," . $y . "," . $z;
						$str = str_replace("(region)", $args[1], $this->api->getAPI()->getString("list"));
						$str = str_replace("(flag)", $flag, $str);
						$str = str_replace("(members)", $member, $str);
						$str = str_replace("(pos1)", $all, $str);
						$str = str_replace("(pos2)", $all1, $str);
						$str = str_replace("(world)", $world, $str);
						$p->sendMessage($str);
						break;
					case "addmembers";
					case "addm";
					case "addmem";
						if (!isset($args[1]) || !isset($args[2])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("m"));
						} else {
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("notname"));
							if (!$worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
								return;
							}
							if ($gd->getMembers($args[1], $args[2])) {
								$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("ism"));
								return;
							}
							$str = str_replace("(pp)", $args[2], $this->api->getAPI()->getString("addm"));
							$str = str_replace("(worldguard)", $args[1], $str);
							$worlddata->addMember($args[1], strtolower($args[2]));
							$p->sendMessage($worldguard->getTag() . $str);
						}
						break;
					case "removem";
					case "removemembers";
					case "removemem";
						if (!isset($args[1]) || !isset($args[2])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("m"));
						} else {
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("isname"));
							if (!$worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
								return;
							}
							if (!$gd->getMembers($args[1], $args[2])) {
								$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("notremove"));
								return;
							}
							$worlddata->RemoveMember($args[1], $args[2]);
							$str = str_replace("(pp)", $args[2], $this->api->getAPI()->getString("remove"));
							$str = str_replace("(worldguard)", $args[1], $str);
							$str = str_replace("(name)", $p->getName(), $str);
							$p->sendMessage($worldguard->getTag() . $str);
						}
						break;
					case "remove";
						if (!isset($args[1])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("noname"));
						} else {
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("notname"));
							if (!$worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
								return;
							}
							$str = str_replace("(worldguard)", $args[1], $this->api->getAPI()->getString("removeguard"));
							$worlddata->removeGuard($args[1]);
							$p->sendMessage($worldguard->getTag() . $str);
						}
						break;
					case "list";
						$datalist = $this->api->db["name"] ?? null;
						if ($datalist == null) {
							$p->sendMessage($this->api->getAPI()->getString("notList"));
							return;
						}
						$b = [];
						foreach ($datalist as $list => $data) {
							$b[] = $list;
						}
						$worldlist = implode(", ", $b);
						$str = str_replace("(list)", $worldlist, $this->api->getAPI()->getString("listWorld"));
						$p->sendMessage($str);
						break;
					case "block";
					case "block-allown";
						if (!isset($args[1])) {
							$p->sendMessage($worldguard->getTag() . $this->api->getAPI()->getString("region"));
						} else {
							$item_block = $p->getInventory()->getItemInHand()->getBlock();
							if ($item_block instanceof Air) {
								$p->sendMessage(($worldguard->getTag() . $this->api->getAPI()->getString("allblock")));
							} else {
								// 블럭일경우
								$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("notname"));
								if (!$worlddata->isName($args[1])) {
									$p->sendMessage($worldguard->getTag() . $is);
									return;
								}
								$msg = str_replace("(name)", $args[1], $this->api->getAPI()->getString("allblock_add"));
								$msg = str_replace("(block)", $item_block->getName(), $msg);
								if (! $blockdata->setData_Block($args[1], $item_block->getName())){
									$p->sendMessage($worldguard->getTag(). $msg);
								}else{
									// 제거
									$msg = str_replace("(name)", $args[1], $this->api->getAPI()->getString("allblock_remove"));
									$msg = str_replace("(block)", $item_block->getName(), $msg);
									if ($blockdata->Remove_Data($args[1], $item_block->getName())){
										// 삭제 메시지
										$p->sendMessage($worldguard->getTag(). $msg);
									}else{
										$msg = str_replace("(name)", $args[1], $this->api->getAPI()->getString("allblock_removeno"));
										$msg = str_replace("(block)", $item_block->getName(), $msg);
										$p->sendMessage($msg);
									}
								}
							}
						}
						break;
						case "tp";
						case "teleport";
						if(!isset($args[1])){
							$p->sendMessage($worldguard->getTag(). $this->api->getAPI()->getString("wordtp"));
						}else{
							$is = str_replace("(name)", $args[1], $this->api->getAPI()->getString("notname"));
							if (!$worlddata->isName($args[1])) {
								$p->sendMessage($worldguard->getTag() . $is);
							}else{
								// teleport
								$position = $this->api->db["name"][$args[1]];
								$pos1 = $position["pos1"];
								$pos2 = $position["pos2"];
								$e1 = explode(":", $pos1);
								$x1 = $e1[0] ?? 0;
								$y1 = $e1[1] ?? 0;
								$z1 = $e1[2] ?? 0;
								$e2 = explode(":", $pos2);
								$x2 = $e2[0] ?? 0;
								$y2 = $e2[1] ?? 0;
								$z2 = $e2[2] ?? 0;
								$world = $e1[3] ?? "";
								$midX = (int)(($x1 + $x2) / 2);
								$midY = (int)(($y1 + $y2) / 2);
								$midZ = (int)(($z1 + $z2) / 2);
								$msg = str_replace("(name)", $args[1], $this->api->getAPI()->getString("worldtpa"));
								$p->sendMessage($worldguard->getTag(). $msg);
								$p->teleport(new Position($midX + 0.5, $midY, $midZ + 0.5,  Server::getInstance()->getWorldManager()->getWorldByName($world)));
							}
						}
				}
			}
		}
	}
}