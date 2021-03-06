<?php
    namespace ps88\psarea\events\area;

    use pocketmine\Player;
    use ps88\psarea\loaders\base\BaseArea;

    class PSAreaEnterEvent extends PSAreaEvent {
        /** @var Player */
        private $player;

        public function __construct(BaseArea $area, Player $player) {
            parent::__construct($area);
            $this->player = $player;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player {
            return $this->player;
        }
    }