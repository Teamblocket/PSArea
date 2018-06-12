<?php
    namespace ps88\psarea\Loaders\Skyland;

    use pocketmine\level\generator\Generator;
    use pocketmine\level\generator\GeneratorManager;
    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\Server;
    use pocketmine\utils\Config;
    use ps88\psarea\Generator\SkylandGenerator;
    use ps88\psarea\Loaders\base\BaseArea;
    use ps88\psarea\Loaders\base\BaseLoader;

    class SkylandLoader extends BaseLoader {
        /** @var SkylandArea[] */
        public $areas = [];

        public static $landcount = 0;

        /**
         * @param string $name
         * @return SkylandArea[]
         */
        public function getAreasByOwner(string $name): array {
            return parent::getAreasByOwner($name);
        }

        /**
         * @param string $name
         * @return SkylandArea[]
         */
        public function getAreasSharedAndOwned(string $name): array {
            return parent::getAreasSharedAndOwned($name);
        }

        /**
         * @param int $id
         * @return null|SkylandArea|BaseArea
         */
        public function getAreaById(int $id): ?BaseArea {
            return (($a = parent::getAreaById($id)) instanceof SkylandArea) ? $a : \null;
        }

        /**
         * @param BaseArea|SkylandArea $area
         * @return bool
         */
        public function addArea(BaseArea $area): bool {
            if (!$area instanceof SkylandArea) return \false;
            return parent::addArea($area);
        }

        /**
         * @param Vector3 $vec
         * @return null|BaseArea|SkylandArea
         */
        public function getAreaByVector3(Vector3 $vec): ?BaseArea {
            return (($a = parent::getAreaByVector3($vec)) instanceof SkylandArea) ? $a : \null;
        }

        /**
         * @return SkylandArea[]
         */
        public function getAreas(): array {
            return $this->areas;
        }

        public function saveAll(): bool {
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "skyland" . "/" . "data.json", Config::JSON);
            $c->setAll([]);
            foreach ($this->getAreas() as $area) {
                $o = ($area->owner == \null) ? \null : $area->owner->getName();
                $s = [];
                foreach ($area->getShares() as $share) {
                    array_push($s, $share->getName());
                }
                $c->set($area->getLandnum(), [
                        'cenv' => [$area->getCenter()->x, $area->getCenter()->y],
                        'owner' => $o,
                        'shares' => $s
                ]);
            }
            $c->save();
            return \true;
        }

        public function loadLevel(): void {
            GeneratorManager::addGenerator(SkylandGenerator::class, 'skyland');
            $g = GeneratorManager::getGenerator("skyland");
            if (!Server::getInstance()->loadLevel("skyland")) {
                @mkdir(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "skyland");
                Server::getInstance()->generateLevel("skyland", \null, $g, []);
            }
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "skyland" . "/" . "data.json", Config::JSON);
            foreach ($c->getAll() as $key => $value) {
                $s = [];
                foreach ($value['shares'] as $share) {
                    array_push($s, Server::getInstance()->getOfflinePlayer($share));
                }
                $o = ($value['owner'] == \null) ? \null : Server::getInstance()->getOfflinePlayer($value['owner']);
                $this->addArea(new SkylandArea($key, new Vector2($value['cenv'][0], $value['cenv'][1]), $o, $s));
                if (self::$landcount < $key) self::$landcount = $key;
            }
        }
    }