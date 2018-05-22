<?php
    namespace ps88\psarea\Loaders\Field;

    use pocketmine\level\Position;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Events\LandWarpEvent;
    use ps88\psarea\Loaders\base\BaseArea;

    class FieldArea extends BaseArea {
        public const LandType = self::Field;

        public function Warp(Player $pl): bool {
            $v = $this->getMinVector();
            Server::getInstance()->getPluginManager()->callEvent($ev = new LandWarpEvent($this, $pl));
            if ($ev->isCancelled()) return \false;
            $pl->teleport(new Position($v->x, 14, $v->y, Server::getInstance()->getLevelByName('field')));
            return \true;
        }
    }