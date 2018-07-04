<?php
    namespace ps88\psarea\events\area;

    use pocketmine\IPlayer;
    use ps88\psarea\loaders\base\BaseArea;

    class PSAreaDelShareEvent extends PSAreaEvent {

        /** @var IPlayer */
        private $share;

        public function __construct(BaseArea $area, IPlayer $share) {
            parent::__construct($area);
            $this->share = $share;
        }

        /**
         * @return IPlayer
         */
        public function getShare(): IPlayer {
            return $this->share;
        }
    }