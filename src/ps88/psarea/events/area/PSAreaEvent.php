<?php
    namespace ps88\psarea\events\area;

    use pocketmine\event\Cancellable;
    use pocketmine\event\Event;
    use ps88\psarea\loaders\base\BaseArea;

    abstract class PSAreaEvent extends Event implements Cancellable {
        /** @var BaseArea */
        protected $area;

        public function __construct(BaseArea $area) {
            $this->area = $area;
        }

        /**
         * @return BaseArea
         */
        public function getArea(): BaseArea {
            return $this->area;
        }

        /**
         * @param BaseArea $area
         */
        public function setArea(BaseArea $area): void {
            $this->area = $area;
        }
    }