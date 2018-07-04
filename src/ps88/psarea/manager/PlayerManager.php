<?php
    namespace ps88\psarea\manager;

    use pocketmine\Player;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\loaders\LoaderManager;

    class PlayerManager extends \Thread {
        public static $PlayingPlayers = [];

        public static function PlayerEnter(?BaseArea $area, Player $player) {
            if ($area !== \null)
                if (self::$PlayingPlayers[$player->getName()][0] == $area::LandType and self::$PlayingPlayers[$player->getName()][1] == $area->getLandnum()) return;
            $key = ($area !== \null) ? [$area::LandType, $area->getLandnum()] : \null;
            if (isset(self::$PlayingPlayers[$player->getName()]))
                if (($v = self::$PlayingPlayers[$player->getName()]) !== \null)
                    LoaderManager::getLoaderByType($v[0])->getAreaById($v[1])->PlayerOuit($player);
            unset(self::$PlayingPlayers[$player->getName()]);
            self::$PlayingPlayers[$player->getName()] = $key;
            if ($area !== \null) $area->PlayerAccess($player);
        }
    }