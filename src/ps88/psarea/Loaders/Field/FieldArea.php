<?php
    namespace ps88\psarea\loaders\field;

    use pocketmine\level\Position;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\events\area\PSAreaWarpEvent;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\translator\Translator;

    class FieldArea extends BaseArea {
        public const LandType = self::Field;

        public function Warp(Player $pl): bool {
            $v = $this->getMinVector();
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaWarpEvent($this, $pl));
            if ($ev->isCancelled()) return \false;
            $pl->teleport(new Position($v->x, 14, $v->y, Server::getInstance()->getLevelByName('field')));
            return \true;
        }

        protected function TypeAsString(): string {
            return Translator::get("field", \false);
        }
    }