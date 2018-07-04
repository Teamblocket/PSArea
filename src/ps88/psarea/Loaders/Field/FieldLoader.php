<?php
    namespace ps88\psarea\loaders\field;

    use pocketmine\level\generator\GeneratorManager;
    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\Server;
    use pocketmine\utils\Config;
    use ps88\psarea\commands\field\FieldAddShareCommand;
    use ps88\psarea\commands\field\FieldBuyCommand;
    use ps88\psarea\commands\field\FieldDelShareCommand;
    use ps88\psarea\commands\field\FieldGiveCommand;
    use ps88\psarea\commands\field\FieldInfoCommand;
    use ps88\psarea\commands\field\FieldRegisteredList;
    use ps88\psarea\commands\field\FieldWarpCommand;
    use ps88\psarea\generator\FieldGenerator;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\loaders\base\BaseLoader;
    use ps88\psarea\PSAreaMain;
    use ps88\psarea\translator\Translator;

    class FieldLoader extends BaseLoader {
        /** @var FieldArea[] */
        public $areas = [];

        public static $landcount = 0;

        public static $diagonalcount = 0;

        /** @var FieldLoader|null */
        private static $Instance = \null;

        public function __construct(int $price) {
            parent::__construct($price);
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
            $p = Server::getInstance()->getPluginManager()->getPlugin("PSArea");
            if(! $p instanceof PSAreaMain) return;
            Server::getInstance()->getCommandMap()->registerAll("PSArea", [
                    new FieldRegisteredList( Translator::getCommands("field-registeredlist-name"), Translator::getCommands("field-registeredlist-description"), Translator::getCommands("field-registeredlist-usage"), Translator::getCommands("field-registeredlist-aliases")),
                    new FieldAddShareCommand( Translator::getCommands("field-addshare-name"), Translator::getCommands("field-addshare-description"), Translator::getCommands("field-addshare-usage"), Translator::getCommands("field-addshare-aliases")),
                    new FieldBuyCommand( Translator::getCommands("field-buy-name"), Translator::getCommands("field-buy-description"), Translator::getCommands("field-buy-usage"), Translator::getCommands("field-buy-aliases")),
                    new FieldGiveCommand( Translator::getCommands("field-give-name"), Translator::getCommands("field-give-description"), Translator::getCommands("field-give-usage"), Translator::getCommands("field-give-aliases")),
                    new FieldInfoCommand( Translator::getCommands("field-info-name"), Translator::getCommands("field-info-description"), Translator::getCommands("field-info-usage"), Translator::getCommands("field-info-aliases")),
                    new FieldWarpCommand( Translator::getCommands("field-warp-name"), Translator::getCommands("field-warp-description"), Translator::getCommands("field-warp-usage"), Translator::getCommands("field-warp-aliases")),
                    new FieldDelShareCommand( Translator::getCommands("field-delshare-name"), Translator::getCommands("field-delshare-description"), Translator::getCommands("field-delshare-usage"), Translator::getCommands("field-delshare-aliases"))
            ]);
            GeneratorManager::addGenerator(FieldGenerator::class, "field");
            $g = GeneratorManager::getGenerator("field");
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
