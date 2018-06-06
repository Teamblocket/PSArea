<?php
    namespace ps88\psarea\Loaders\base;

    use pocketmine\math\Vector3;
    use pocketmine\Server;
    use ps88\psarea\Events\LandAddEvent;

    abstract class BaseLoader {

        /** @var BaseArea[] */
        public $areas = [];

        public static $landcount;

        public const Maximum_Lands = 3;

        public const Land_Price = 30000;

        /**
         * @param string $name
         * @return BaseArea[]
         */
        public function getAreasByOwner(string $name): array {
            $a = [];
            foreach ($this->areas as $area) {
                if ($area->owner == null) continue;
                if ($area->owner->getName() == $name) array_push($a, $area);
            }
            return $a;
        }

        /**
         * @param string $name
         * @return BaseArea[]
         */
        public function getAreasSharedAndOwned(string $name): array {
            $a = $this->getAreasByOwner($name);
            foreach ($this->areas as $area) {
                if ($area->getShare($name) !== \null) array_push($a, $area);
            }
            return $a;
        }

        /**
         * @param int $id
         * @return null|BaseArea
         */
        public function getAreaById(int $id): ?BaseArea {
            foreach ($this->areas as $area) {
                if ($area->getLandnum() == $id) return $area;
            }
            return \null;
        }

        public function getAreaByVector3(Vector3 $vec): ?BaseArea {
            foreach ($this->areas as $area) {
                $mn = $area->getMinVector();
                $mx = $area->getMaxVector();
                if ($mn->x <= $vec->x and $mn->y <= $vec->z and $mx->x >= $vec->x and $mx->y >= $vec->z) return $area;
            }
            return \null;
        }

        /**
         * @param BaseArea $area
         * @return bool
         */
        public function addArea(BaseArea $area): bool {
            if ($this->getAreaById($area->getLandnum()) !== \null) return \false;
            Server::getInstance()->getPluginManager()->callEvent($ev = new LandAddEvent($area));
            if ($ev->isCancelled()) return \false;
            array_push($this->areas, $area);
            return \true;
        }

        /**
         * @return BaseArea[]
         */
        public function getAreas(): array {
            return $this->areas;
        }

        /**
         * @return bool
         */
        abstract public function saveAll(): bool;

        abstract public function loadLevel(): void;
    }