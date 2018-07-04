<?php
    namespace ps88\psarea\MoneyTranslate;

    use nlog\StormCore\StormCore;
    use nlog\StormCore\StormPlayer;
    use onebone\economyapi\EconomyAPI;
    use pocketmine\Player;
    use pocketmine\plugin\Plugin;
    use pocketmine\Server;

    class MoneyTranslator {
        public const EconomyPluginList = [
                "StormCore",
                "EconomyAPI"
        ];

        /** @var Plugin */
        private static $moneyAPI;

        public static function run() {
            foreach (self::EconomyPluginList as $s)
                if (Server::getInstance()->getPluginManager()->getPlugin($s) !== \null) self::$moneyAPI = Server::getInstance()->getPluginManager()->getPlugin($s);
            if (self::$moneyAPI == \null) {
                Server::getInstance()->getLogger()->emergency("No Economy Plugin!!");
                Server::getInstance()->getPluginManager()->disablePlugin(Server::getInstance()->getPluginManager()->getPlugin("PSArea"));
            }
        }

        public static function getMoney($player): ?int {
            $pl = ($player instanceof Player) ? $player : Server::getInstance()->getPlayer($player);
            if ($pl == \null and $pl = Server::getInstance()->getOfflinePlayer($player) == \null) return \null;
            if (self::$moneyAPI instanceof EconomyAPI) {
                return self::$moneyAPI->myMoney($player);
            } elseif (self::$moneyAPI instanceof StormCore && $pl instanceof StormPlayer) {
                return $pl->getMoney();
            }
            return \null;
        }

        public static function addMoney($player, int $money): bool {
            $pl = ($player instanceof Player) ? $player : Server::getInstance()->getPlayer($player);
            if ($pl == \null and $pl = Server::getInstance()->getOfflinePlayer($player) == \null) return \false;
            if (self::$moneyAPI instanceof EconomyAPI) {
                self::$moneyAPI->addMoney($player, $money);
                return true;
            } elseif (self::$moneyAPI instanceof StormCore && $pl instanceof StormPlayer) {
                $pl->addMoney($money);
                return true;
            }
            return false;
        }

        public static function reduceMoney($player, int $money): bool {
            $pl = ($player instanceof Player) ? $player : Server::getInstance()->getPlayer($player);
            if ($pl == \null and $pl = Server::getInstance()->getOfflinePlayer($player) == \null) return \false;
            if (self::$moneyAPI instanceof EconomyAPI) {
                self::$moneyAPI->reduceMoney($player, $money);
                return true;
            } elseif (self::$moneyAPI instanceof StormCore && $pl instanceof StormPlayer) {
                $pl->reduceMoney($money);
                return true;
            }
            return false;
        }

        public static function setMoney($player, int $money): bool {
            $pl = ($player instanceof Player) ? $player : Server::getInstance()->getPlayer($player);
            if ($pl == \null and $pl = Server::getInstance()->getOfflinePlayer($player) == \null) return \false;
            if (self::$moneyAPI instanceof EconomyAPI) {
                self::$moneyAPI->setMoney($player, $money);
                return true;
            } elseif (self::$moneyAPI instanceof StormCore && $pl instanceof StormPlayer) {
                $pl->setMoney($money);
                return true;
            }
            return false;
        }
    }
