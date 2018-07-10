<?php
    namespace ps88\psarea\loaders\land;

    use pocketmine\level\Level;
    use pocketmine\level\Position;
    use pocketmine\math\Vector2;
    use pocketmine\Player;
    use pocketmine\Server;
    use pocketmine\utils\Config;
    use ps88\psarea\commands\land\LandAddShareCommand;
    use ps88\psarea\commands\land\LandBuyCommand;
    use ps88\psarea\commands\land\LandDelShareCommand;
    use ps88\psarea\commands\land\LandGiveCommand;
    use ps88\psarea\commands\land\LandInfoCommand;
    use ps88\psarea\commands\land\LandMakeCommand;
    use ps88\psarea\commands\land\LandWarpCommand;
    use ps88\psarea\listeners\LandListener;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\loaders\base\BaseLoader;
    use ps88\psarea\loaders\land\LandArea;
    use ps88\psarea\PSAreaMain;
    use ps88\psarea\translator\Translator;

    class LandLoader extends BaseLoader {
        /** @var LandArea[] */
        public $areas = [];

        /** @var array */
        public $register = [];

        public static $landcount = 0;

        public function __construct(int $price) {
            parent::__construct($price);
            $p = Server::getInstance()->getPluginManager()->getPlugin("PSArea");
            if (!$p instanceof PSAreaMain) return;
            Server::getInstance()->getPluginManager()->registerEvents(new LandListener(), $p);
        }

        /**
         * @param string $name
         * @return LandArea[]
         */
        public function getAreasByOwner(string $name): array {
            return parent::getAreasByOwner($name);
        }

        /**
         * @param string $name
         * @return LandArea[]
         */
        public function getAreasSharedAndOwned(string $name): array {
            return parent::getAreasSharedAndOwned($name);
        }

        /**
         * @param int $id
         * @return null|LandArea|BaseArea
         */
        public function getAreaById(int $id): ?BaseArea {
            return (($a = parent::getAreaById($id)) instanceof LandArea) ? $a : \null;
        }

        /**
         * @param BaseArea|LandArea $area
         * @return bool
         */
        public function addArea(BaseArea $area): bool {
            if (!$area instanceof LandArea) return \false;
            return parent::addArea($area);
        }

        /**
         * @param Position $vec
         * @return null|BaseArea|LandArea
         */
        public function getAreaByPosition(Position $vec): ?BaseArea {
            foreach ($this->getAreas() as $area) {
                if ($area->getLevel()->getName() == $vec->getLevel()->getName()) {
                    $mn = $area->getMinVector();
                    $mx = $area->getMaxVector();
                    if ($mn->x <= $vec->x and $mn->y <= $vec->z and $mx->x >= $vec->x and $mx->y >= $vec->z) return $area;
                }
            }
            return \null;
        }

        /**
         * @return LandArea[]
         */
        public function getAreas(): array {
            return $this->areas;
        }

        /**
         * @return bool
         */
        public function saveAll(): bool {
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "NormalLand" . "/" . "data.json", Config::JSON);
            $c->setAll([]);
            foreach ($this->getAreas() as $area) {
                $o = ($area->owner == \null) ? \null : $area->owner->getName();
                $s = [];
                foreach ($area->getShares() as $share) {
                    array_push($s, $share->getName());
                }
                $c->set($area->getLandnum(), [
                        'levelid' => $area->getLevel()->getId(),
                        'minv' => [$area->getMinVector()->x, $area->getMinVector()->y],
                        'maxv' => [$area->getMaxVector()->x, $area->getMaxVector()->y],
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
                    new LandAddShareCommand( Translator::getCommands("land-addshare-name"), Translator::getCommands("land-addshare-description"), Translator::getCommands("land-addshare-usage"), Translator::getCommands("land-addshare-aliases")),
                    new LandBuyCommand( Translator::getCommands("land-buy-name"), Translator::getCommands("land-buy-description"), Translator::getCommands("land-buy-usage"), Translator::getCommands("land-buy-aliases")),
                    new LandGiveCommand( Translator::getCommands("land-give-name"), Translator::getCommands("land-give-description"), Translator::getCommands("land-give-usage"), Translator::getCommands("land-give-aliases")),
                    new LandInfoCommand( Translator::getCommands("land-info-name"), Translator::getCommands("land-info-description"), Translator::getCommands("land-info-usage"), Translator::getCommands("land-info-aliases")),
                    new LandWarpCommand( Translator::getCommands("land-warp-name"), Translator::getCommands("land-warp-description"), Translator::getCommands("land-warp-usage"), Translator::getCommands("land-warp-aliases")),
                    new LandDelShareCommand( Translator::getCommands("land-delshare-name"), Translator::getCommands("land-delshare-description"), Translator::getCommands("land-delshare-usage"), Translator::getCommands("land-delshare-aliases")),
                    new LandMakeCommand( Translator::getCommands("land-make-name"), Translator::getCommands("land-make-description"), Translator::getCommands("land-make-usage"), Translator::getCommands("land-make-aliases"))
            ]);
            @mkdir(Server::getInstance()->getDataPath() . "/" . "NormalLand");
            $c = new Config(Server::getInstance()->getDataPath() . "/" . "NormalLand" . "/" . "data.json", Config::JSON);
            foreach ($c->getAll() as $key => $value) {
                $s = [];
                foreach ($value['shares'] as $share) {
                    array_push($s, Server::getInstance()->getOfflinePlayer($share));
                }
                $o = ($value['owner'] == \null) ? \null : Server::getInstance()->getOfflinePlayer($value['owner']);
                $this->addArea(new LandArea($key, Server::getInstance()->getLevel($value['levelid']), new Vector2($value['minv'][0], $value['minv'][1]), new Vector2($value['maxv'][0], $value['maxv'][1]), $o, $s));
                if (self::$landcount < $key) self::$landcount = $key;
            }
        }

        public function startRegister(Player $pl, int $landnum, Level $level) {
            $this->register[$pl->getName()] = [];
            $this->register[$pl->getName()][0] = $landnum;
            $this->register[$pl->getName()][1] = $level->getId();
        }

        public function DoingRegister(Player $pl): bool {
            return isset($this->register[$pl->getName()]);
        }

        public function registeringLevel(Player $pl): Level {
            return Server::getInstance()->getLevel($this->register[$pl->getName()][1]);
        }

        public function FirstVecRegister(Player $pl, Vector2 $vec) {
            $this->register[$pl->getName()][2] = $vec;
        }

        public function isFirstVecRegister(Player $pl) {
            return isset($this->register[$pl->getName()][2]);
        }

        public function SecondVecRegister(Player $pl, Vector2 $vec) {
            $this->register[$pl->getName()][3] = $vec;
        }

        public function isSecondVecRegister(Player $pl) {
            return isset($this->register[$pl->getName()][3]);
        }

        public function getRegisters(Player $pl): ?array {
            if ($this->register[$pl->getName()] == \null) return \null;
            $a = $this->register[$pl->getName()];
            unset($a[0]);
            unset($a[1]);
            return $a;
        }

        public function registeringNum(Player $pl): int {
            return $this->register[$pl->getName()][0];
        }

        public function stopRegister(Player $pl) {
            unset($this->register[$pl->getName()]);
        }
    }