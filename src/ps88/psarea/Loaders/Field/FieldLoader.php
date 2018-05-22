<?php
    namespace ps88\psarea\Loaders\Field;

    use pocketmine\level\generator\Generator;
    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\Server;
    use pocketmine\utils\Config;
    use ps88\psarea\Generator\FieldGenerator;
    use ps88\psarea\Loaders\base\BaseArea;
    use ps88\psarea\Loaders\base\BaseLoader;

    class FieldLoader extends BaseLoader {
        /** @var FieldArea[] */
        public $areas = [];

        public static $landcount = 0;

        public static $diagonalcount = 0;

        /** @var FieldLoader|null */
        private static $Instance = \null;

        public function __construct() {
            self::$Instance = $this;
        }

        /**
         * @param string $name
         * @return FieldArea[]
         */
        public function getAreasByOwner(string $name): array {
            return parent::getAreasByOwner($name);
        }

        /**
         * @param string $name
         * @return FieldArea[]
         */
        public function getAreasSharedAndOwned(string $name): array {
            return parent::getAreasSharedAndOwned($name);
        }

        /**
         * @param int $id
         * @return null|FieldArea|BaseArea
         */
        public function getAreaById(int $id): ?BaseArea {
            return (($a = parent::getAreaById($id)) instanceof FieldArea) ? $a : \null;
        }

        /**
         * @param BaseArea|FieldArea $area
         * @return bool
         */
        public function addArea(BaseArea $area): bool {
            if (!$area instanceof FieldArea) return \false;
            return parent::addArea($area);
        }

        /**
         * @param Vector3 $vec
         * @return null|BaseArea|FieldArea
         */
        public function getAreaByVector3(Vector3 $vec): ?BaseArea {
            return (($a = parent::getAreaByVector3($vec)) instanceof FieldArea) ? $a : \null;
        }

        /**
         * @return FieldArea[]
         */
        public function getAreas(): array {
            return $this->areas;
        }

        /**
         * @return bool
         */
        public function saveAll(): bool {
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "field" . "/" . "data.json", Config::JSON);
            $c->setAll([]);
            foreach ($this->getAreas() as $area) {
                $o = ($area->owner == \null) ? \null : $area->owner->getName();
                $s = [];
                foreach ($area->getShares() as $share) {
                    array_push($s, $share->getName());
                }
                $c->set($area->getLandnum(), [
                        'minv' => [$area->getMinVector()->x, $area->getMinVector()->y],
                        'maxv' => [$area->getMaxVector()->x, $area->getMaxVector()->y],
                        'owner' => $o,
                        'shares' => $s
                ]);
            }
            $c->save();
            return \false;
        }

        public function loadLevel(): void {
            Generator::addGenerator(FieldGenerator::class, "field");
            $g = Generator::getGenerator("field");
            var_dump($g);
            if (!Server::getInstance()->loadLevel("field")) {
                @mkdir(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "field");
                Server::getInstance()->generateLevel("field", \null, $g, []);
            }
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "worlds" . "/" . "field" . "/" . "data.json", Config::JSON);
            foreach ($c->getAll() as $key => $value) {
                $s = [];
                foreach ($value['shares'] as $share) {
                    array_push($s, Server::getInstance()->getOfflinePlayer($share));
                }
                $o = ($value['owner'] == \null) ? \null : Server::getInstance()->getOfflinePlayer($value['owner']);
                $this->addArea(new FieldArea($key, new Vector2($value['minv'][0], $value['minv'][1]), new Vector2($value['maxv'][0], $value['maxv'][1]), $o, $s));
                if (self::$landcount < $key) self::$landcount = $key;
            }
        }

        public function isRegistered($x, $z): bool {
            foreach ($this->getAreas() as $area) {
                if ($area->getMaxVector()->x >= $x and $area->getMaxVector()->y >= $z and $area->getMinVector()->x <= $x and $area->getMinVector()->y <= $z) return \true;
            }
            return \false;
        }

        public static function getInstance(): ?FieldLoader {
            return self::$Instance;
        }
    }