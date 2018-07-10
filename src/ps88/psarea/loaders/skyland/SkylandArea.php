<?php
    namespace ps88\psarea\loaders\skyland;

    use pocketmine\level\Position;
    use pocketmine\math\Vector2;
    use pocketmine\IPlayer;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\events\area\PSAreaWarpEvent;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\translator\Translator;

    class SkylandArea extends BaseArea {
        public const LandType = self::Skyland;

        /** @var Vector2 */
        public $center;

        public function __construct(int $landnum, Vector2 $center, ?IPlayer $owner = \null, $shares = []) {
            $this->center = $center;
            $minv = new Vector2($center->x - 99, $center->y - 99);
            $maxv = new Vector2($center->x + 100, $center->y + 100);
            parent::__construct($landnum, $minv, $maxv, $owner, $shares);
        }

        /**
         * @return Vector2
         */
        public function getCenter(): Vector2 {
            return $this->center;
        }

        protected function TypeAsString(): string {
            return Translator::get("skyland", \false);
        }

        public function Warp(Player $pl): bool {
            $v = $this->getCenter();
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaWarpEvent($this, $pl));
            if ($ev->isCancelled()) return \false;
            $x = ($this->getLandnum() % 2 == 0) ? $v->x : $v->x - 5;
            $pl->teleport(new Position($x, 14, $v->y, Server::getInstance()->getLevelByName('skyland')));
            return \true;
        }
    }