<?php
    namespace ps88\psarea\loaders\land;

    use pocketmine\IPlayer;
    use pocketmine\level\Level;
    use pocketmine\level\Position;
    use pocketmine\math\Vector2;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\events\area\PSAreaWarpEvent;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\translator\Translator;

    class LandArea extends BaseArea {
        public const LandType = self::Land;

        /** @var Level */
        private $level;

        public function __construct(int $landnum, Level $level, Vector2 $minvec, Vector2 $maxvec, ?IPlayer $owner = \null, array $shares = []) {
            parent::__construct($landnum, $minvec, $maxvec, $owner, $shares);
            $this->level = $level;
        }

        public function Warp(Player $pl): bool {
            $v = $this->getMinVector();
            $v2 = $this->getMaxVector();
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaWarpEvent($this, $pl));
            if ($ev->isCancelled()) return \false;
            $pl->teleport(new Position(($v->x + $v2->x) / 2, 14, ($v->y + $v2->y) / 2, $this->getLevel()));
            return \true;
        }

        public function getSize(): int {
            $v = $this->getMinVector();
            $v2 = $this->getMaxVector();
            return ($v2->x - $v->x) * ($v2->y - $v->y);
        }

        protected function TypeAsString(): string {
            return Translator::get("land", \false);
        }

        /**
         * @return Level
         */
        public function getLevel(): Level {
            return $this->level;
        }

        /**
         * @param Level $level
         */
        public function setLevel(Level $level): void {
            $this->level = $level;
        }
    }