<?php
    namespace ps88\psarea\loaders\skyland;

    use pocketmine\level\generator\GeneratorManager;
    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\Server;
    use pocketmine\utils\Config;
    use ps88\psarea\commands\skyland\SkylandAddShareCommand;
    use ps88\psarea\commands\skyland\SkylandBuyCommand;
    use ps88\psarea\commands\skyland\SkylandDelShareCommand;
    use ps88\psarea\commands\skyland\SkylandGiveCommand;
    use ps88\psarea\commands\skyland\SkylandInfoCommand;
    use ps88\psarea\commands\skyland\SkylandWarpCommand;
    use ps88\psarea\generator\SkylandGenerator;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\loaders\base\BaseLoader;
    use ps88\psarea\PSAreaMain;
    use ps88\psarea\translator\Translator;

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
            $p = Server::getInstance()->getPluginManager()->getPlugin("PSArea");
            if(! $p instanceof PSAreaMain) return;
            Server::getInstance()->getCommandMap()->registerAll("PSArea", [
                    new SkylandAddShareCommand( Translator::getCommands("skyland-addshare-name"), Translator::getCommands("skyland-addshare-description"), Translator::getCommands("skyland-addshare-usage"), Translator::getCommands("skyland-addshare-aliases")),
                    new SkylandBuyCommand( Translator::getCommands("skyland-buy-name"), Translator::getCommands("skyland-buy-description"), Translator::getCommands("skyland-buy-usage"), Translator::getCommands("skyland-buy-aliases")),
                    new SkylandGiveCommand( Translator::getCommands("skyland-give-name"), Translator::getCommands("skyland-give-description"), Translator::getCommands("skyland-give-usage"), Translator::getCommands("skyland-give-aliases")),
                    new SkylandInfoCommand( Translator::getCommands("skyland-info-name"), Translator::getCommands("skyland-info-description"), Translator::getCommands("skyland-info-usage"), Translator::getCommands("skyland-info-aliases")),
                    new SkylandWarpCommand( Translator::getCommands("skyland-warp-name"), Translator::getCommands("skyland-warp-description"), Translator::getCommands("skyland-warp-usage"), Translator::getCommands("skyland-warp-aliases")),
                    new SkylandDelShareCommand( Translator::getCommands("skyland-delshare-name"), Translator::getCommands("skyland-delshare-description"), Translator::getCommands("skyland-delshare-usage"), Translator::getCommands("skyland-delshare-aliases"))
            ]);
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