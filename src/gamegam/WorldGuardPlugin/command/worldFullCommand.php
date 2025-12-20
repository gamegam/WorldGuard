<?php

namespace gamegam\WorldGuardPlugin\command;

use gamegam\WorldGuardPlugin\Data\GuarddData;
use gamegam\WorldGuardPlugin\Data\GuarddFullData;
use gamegam\WorldGuardPlugin\DataBlock;
use gamegam\WorldGuardPlugin\FlagAPI;
use gamegam\WorldGuardPlugin\Language\ConfigGuard;
use gamegam\WorldGuardPlugin\Main;
use gamegam\WorldGuardPlugin\WorldData;
use gamegam\WorldGuardPlugin\WorldGuard;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwnedTrait;

class worldFullCommand extends Command
{

    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("worldprotection", "Commands that manage a particular world as a whole", null, [
            "wp"
        ]);
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    public function execute(CommandSender $p, string $commandLabel, array $args): void
    {
        $main = Main::getInstance();
        $worldguard = WorldGuard::getInstance();
        $worlddata = WorldData::getInstance();
        $blockdata = DataBlock::getInstance();
        $gd = GuarddData::getInstance();
        $da = GuarddFullData::getInstance();
        $tag = $worldguard->getTag();
        if (!$p instanceof Player) {
            $p->sendMessage($tag . $main->getAPI()->getString(ConfigGuard::ingame));
        } else {
            if (!isset($args[0])) {
                $p->sendMessage($tag . $main->getAPI()->getString("world_full_help"));
                return;
            }
            switch ($args[0]) {
                case "create";
                    if (!isset($args[1])) {
                        $p->sendMessage($tag . $main->getAPI()->getString("input_world"));
                    } else {
                        if (isset($this->api->worlds["name"][$args[1]])) {
                            $p->sendMessage($tag . $main->getAPI()->getString("is_input_world"));
                        } else {
                            $main->worlds["name"][$args[1]] = [];
                            $str = str_replace("(world)", $args[1], $main->getAPI()->getString("world_add"));
                            $p->sendMessage($worldguard->getTag() . $str);
                        }
                    }
                    break;
                case "flag";
				case "f";
                    if (!isset($args[1])) {
                        $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("input_world"));
                    } else {
                        if (isset($main->worlds["name"][$args[1]])) {
                            if (!isset($args[2]) || !isset($args[3])) {
                                $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("flag_input"));
                                return;
                            }
                            $as = strtolower($args[3]);
                            $flag = strtolower($args[2]);
                            if ($flag === "exit") {
                                $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("world_flag_black"));
                                return;
                            }
                            if ($as == "allow" || $as == "deny" || $as == "none") {
                                $type = FlagAPI::getInstance();
                                if (!$type->isType(strtolower($flag))) {
                                    $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("noType"));
                                } else {
                                    $f = str_replace("(flag)", $args[2], $main->getAPI()->getString("world_flag_add"));
                                    $a = str_replace("(world)", $args[1], $f);
                                    $message = str_replace("(3)", $args[3], $a);
                                    $main->worlds["name"][$args[1]]["flag"][$args[2]] = $args[3];
                                    $p->sendMessage($message);
                                }
                            } else {
                                $p->sendMessage($main->getAPI()->getString("no3"));
                            }
                        }
                    }
                    break;
                case "remove";
                    if (!isset($args[1])) {
                        $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("input_world"));
                    } else {
                        if (!isset($main->worlds["name"][$args[1]])) {
                            $str = str_replace("(world)", $args[1], $main->getAPI()->getString("world_not"));
                            $p->sendMessage($worldguard->getTag() . $str);
                        } else {
                            unset($main->worlds["name"][$args[1]]);
                            $str = str_replace("(world)", $args[1], $main->getAPI()->getString("world_remove"));
                            $p->sendMessage($worldguard->getTag() . $str);
                        }
                    }
                    break;
                case "info";
                    $isi = 0; // none
                    if (!isset($args[1])) {
                        $p->sendMessage($worldguard->getTag() . $main->getAPI()->getString("i"));
                        return;
                    }
                    $is = str_replace("(world)", $args[1], $main->getAPI()->getString("world_not"));
                    if (!isset($main->worlds["name"][$args[1]])) {
                        $p->sendMessage($worldguard->getTag() . $is);
                        return;
                    }
                    if ($isi == 0) {
                        $flag = [];
                        foreach ($main->worlds["name"][$args[1]]["flag"] ?? [] as $list => $bool) {
                            $flag[] = $list;
                        }
                        $member = [];
                        foreach ($main->worlds["name"][$args[1]]["member"] ?? [] as $list => $bool) {
                            $member[] = $list;
                        }
                        $flag = implode(", ", $flag);
                        $member = implode(", ", $member);
                        if ($member == "") {
                            $member = $main->getAPI()->getString("noMenvers");
                        } else {
                            $member = $main->getAPI()->getString("playerlist") . "{$member}";
                        }
                        $str = str_replace("(world)", $args[1], $main->getAPI()->getString("world_list"));
                        $str = str_replace("(flag)", $flag, $str);
                        $str = str_replace("(members)", $member, $str);
                        $p->sendMessage($str);
                    }
                    break;
                    case "list";
                        $datalist = $main->worlds["name"] ?? null;
                        if ($datalist == null) {
                            $p->sendMessage($main->getAPI()->getString("world_notList"));
                            return;
                        }
                        $b = [];
                        foreach ($datalist as $list => $data) {
                            $b[] = $list;
                        }
                        $worldlist = implode(", ", $b);
                        $str = str_replace("(list)", $worldlist, $main->getAPI()->getString("listWorld"));
                        $p->sendMessage($str);
            }
        }
    }
}