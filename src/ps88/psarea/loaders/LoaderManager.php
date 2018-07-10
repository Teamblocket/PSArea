<?php
    namespace ps88\psarea\loaders;

    use pocketmine\level\Position;
    use pocketmine\Server;
    use ps88\psarea\config\Setting;
    use ps88\psarea\loaders\base\BaseArea;
    use ps88\psarea\loaders\base\BaseLoader;
    use ps88\psarea\loaders\field\FieldLoader;
    use ps88\psarea\loaders\island\IslandLoader;
    use ps88\psarea\loaders\land\LandLoader;
    use ps88\psarea\loaders\skyland\SkylandLoader;
    use ps88\psarea\Tasks\AreaAddTask;

    class LoaderManager {
        /** @var ?FieldLoader */
        public static $fieldloader = \null;

        /** @var ?LandLoader */
        public static $landloader = \null;

        /** @var ?IslandLoader */
        public static $islandloader = \null;

        /** @var ?SkylandLoader */
        public static $skylandloader = \null;

        public static function Load() {
            $prices = Setting::$setting->get("prices");
            if ($prices["field"] == -1) self::$fieldloader = new FieldLoader($prices["field"]);
            if ($prices["island"] !== -1) self::$islandloader = new IslandLoader($prices["island"]);
            if ($prices["skyland"] !== -1) self::$skylandloader = new SkylandLoader($prices["skyland"]);
            if ($prices["land"] !== -1) self::$landloader = new LandLoader($prices["land"]);
            /** @var BaseLoader[] $loaders */
            $loaders = [
                    self::$fieldloader,
                    self::$skylandloader,
                    self::$islandloader,
                    self::$landloader
            ];
            foreach ($loaders as $item) {
                if ($item == \null) continue;
                $item->loadLevel();
            }
            Server::getInstance()->getPluginManager()->getPlugin("PSArea")->getScheduler()->scheduleRepeatingTask(new AreaAddTask(self::getIslandLoader(), self::getSkylandLoader(), self::getFieldLoader()), 3);
        }

        public static function getFieldLoader(): ?FieldLoader {
            return self::$fieldloader;
        }

        public static function getIslandLoader(): ?IslandLoader {
            return self::$islandloader;
        }

        public static function getSkylandLoader(): ?SkylandLoader {
            return self::$skylandloader;
        }

        public static function getLandLoader(): ?LandLoader {
            return self::$landloader;
        }

        public static function getLoaderByType(int $type = BaseArea::Land): ?BaseLoader {
            switch ($type) {
                case BaseArea::Island:
                    return self::getIslandLoader();
                case BaseArea::Field:
                    return self::getFieldLoader();
                case BaseArea::Skyland:
                    return self::getSkylandLoader();
                case BaseArea::Land:
                    return self::getLandLoader();
            }
            return \null;
        }

        /**
         * @param Position $p
         * @return null|BaseArea
         */
        public static function getArea(Position $p): ?BaseArea {
            switch ($p->getLevel()->getName()) {
                case "island":
                    return self::getIslandloader()->getAreaByVector3($p);
                case "skyland":
                    return self::getSkylandloader()->getAreaByVector3($p);
                case "field":
                    return self::getFieldloader()->getAreaByVector3($p);
                default:
                    return self::getLandLoader()->getAreaByPosition($p);
            }
        }

        public static function saveAll(): void {
            /** @var BaseLoader[] $loaders */
            $loaders = [
                    self::$fieldloader,
                    self::$skylandloader,
                    self::$islandloader,
                    self::$landloader
            ];
            foreach ($loaders as $item) {
                if ($item == \null) continue;
                $item->saveAll();
            }
        }
    }
