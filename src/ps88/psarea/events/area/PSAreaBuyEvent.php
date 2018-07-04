<?php
    namespace ps88\psarea\events\area;

    use pocketmine\Player;
    use ps88\psarea\loaders\base\BaseArea;

    class PSAreaBuyEvent extends PSAreaEvent {

        /** @var Player */
        private $buyer;

        public function __construct(BaseArea $area, Player $buyer) {
            parent::__construct($area);
            $this->buyer = $buyer;
        }

        /**
         * @return Player
         */
        public function getBuyer(): Player {
            return $this->buyer;
        }
    }