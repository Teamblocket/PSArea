<?php
    namespace ps88\psarea;


    use pocketmine\{
            event\Listener,
            lang\BaseLang,
            lang\TextContainer,
            plugin\PluginBase,
            utils\Color,
            utils\Config,
            utils\TextFormat
    };
    use ps88\psarea\Commands\Field\{
            FieldAddShareCommand,
            FieldBuyCommand,
            FieldDelShareCommand,
            FieldGiveCommand,
            FieldInfoCommand,
            FieldRegisteredList,
            FieldWarpCommand
    };
    use ps88\psarea\Commands\Island\{
            IslandAddShareCommand,
            IslandBuyCommand,
            IslandDelShareCommand,
            IslandGiveCommand,
            IslandInfoCommand,
            IslandWarpCommand
    };
    use ps88\psarea\Commands\Land\{
            LandAddShareCommand,
            LandBuyCommand,
            LandDelShareCommand,
            LandGiveCommand,
            LandInfoCommand,
            LandMakeCommand,
            LandWarpCommand
    };
    use ps88\psarea\Commands\Skyland\{
            SkylandAddShareCommand,
            SkylandBuyCommand,
            SkylandDelShareCommand,
            SkylandGiveCommand,
            SkylandInfoCommand,
            SkylandWarpCommand
    };
    use ps88\psarea\Loaders\{
            base\BaseLoader,
            Field\FieldLoader,
            Land\LandListener,
            Land\LandLoader,
            Island\IslandLoader,
            Skyland\SkylandLoader
    };
    use ps88\psarea\MoneyTranslate\MoneyTranslator;
    use ps88\psarea\ProtectWorld\ProtectWorld;
    use ps88\psarea\Commands\ProtectWorld\setProtectWorldCommand;
    use ps88\psarea\Tasks\AreaAddTask;
    use ps88\psarea\Tasks\FieldAutoAddTask;

    class PSAreaMain extends PluginBase implements Listener {

        /** @var FieldLoader */
        public $fieldloader;

        /** @var IslandLoader */
        public $islandloader;

        /** @var SkylandLoader */
        public $skylandloader;

        /** @var LandLoader */
        public $landloader;

        /** @var MoneyTranslator */
        public $moneytranslator;

        /** @var ProtectWorld */
        public $protectworld;

        /** @var Config */
        public static $langcf;

        /** @var Config */
        public $setting;

        public function onLoad() {
            $this->fieldloader = new FieldLoader();
            $this->islandloader = new IslandLoader();
            $this->skylandloader = new SkylandLoader();
            $this->landloader = new LandLoader();
        }

        public function onEnable() {
            $this->protectworld = new ProtectWorld($this);
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getServer()->getPluginManager()->registerEvents(new LandListener($this), $this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new AreaAddTask($this), 3);
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new FieldAutoAddTask($this), 20);
            @mkdir($this->getDataFolder());
            $this->setting = new Config($this->getDataFolder()."setting.yml", Config::YAML, [
                    "lang" => "eng"
            ]);
            $lang = $this->setting->get("lang");
            if(!file_exists($this->getDataFolder()."lang_{$lang}.yml")) {
                file_put_contents($this->getDataFolder() . "lang.yml", stream_get_contents($this->getResource("lang_{$lang}.yml")));
            }
            self::$langcf = new Config($this->getDataFolder(). "lang_{$lang}.yml", Config::YAML);
            $this->loadLevels();
            $this->registerCommands();
            if ($this->getServer()->getPluginManager()->getPlugin('StormCore') !== \null) {
                $this->moneytranslator = new MoneyTranslator($this->getServer()->getPluginManager()->getPlugin('StormCore'));
            } elseif ($this->getServer()->getPluginManager()->getPlugin('EconomyAPI') !== \null) {
                $this->moneytranslator = new MoneyTranslator($this->getServer()->getPluginManager()->getPlugin('EconomyAPI'));
            } else {
                $this->getLogger()->emergency("No Money(EconomyAPI etc..) Plugin");
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        }

        public function onDisable() {
            $this->setting->save();
            self::$langcf->save();
            $this->islandloader->saveAll();
            $this->skylandloader->saveAll();
            $this->fieldloader->saveAll();
            $this->landloader->saveAll();
        }

        public function loadLevels(): void {
            /** @var BaseLoader[] $loaders */
            $loaders = [
                    $this->fieldloader,
                    $this->skylandloader,
                    $this->islandloader,
                    $this->landloader
            ];
            foreach ($loaders as $item) {
                $item->loadLevel();
            }
        }

        public function registerCommands(): void {
            $this->getServer()->getCommandMap()->registerAll('PSArea', [
                    new FieldAddShareCommand($this, self::getCommands("field-addshare-name"), self::getCommands("field-addshare-description"), self::getCommands("field-addshare-usage"), self::getCommands("field-addshare-aliases")),
                    new FieldBuyCommand($this, self::getCommands("field-buy-name"), self::getCommands("field-buy-description"), self::getCommands("field-buy-usage"), self::getCommands("field-buy-aliases")),
                    new FieldGiveCommand($this, self::getCommands("field-give-name"), self::getCommands("field-give-description"), self::getCommands("field-give-usage"), self::getCommands("field-give-aliases")),
                    new FieldInfoCommand($this, self::getCommands("field-info-name"), self::getCommands("field-info-description"), self::getCommands("field-info-usage"), self::getCommands("field-info-aliases")),
                    new FieldWarpCommand($this, self::getCommands("field-warp-name"), self::getCommands("field-warp-description"), self::getCommands("field-warp-usage"), self::getCommands("field-warp-aliases")),
                    new FieldDelShareCommand($this, self::getCommands("field-delshare-name"), self::getCommands("field-delshare-description"), self::getCommands("field-delshare-usage"), self::getCommands("field-delshare-aliases")),
                    new IslandAddShareCommand($this, self::getCommands("island-addshare-name"), self::getCommands("island-addshare-description"), self::getCommands("island-addshare-usage"), self::getCommands("island-addshare-aliases")),
                    new IslandBuyCommand($this, self::getCommands("island-buy-name"), self::getCommands("island-buy-description"), self::getCommands("island-buy-usage"), self::getCommands("island-buy-aliases")),
                    new IslandGiveCommand($this, self::getCommands("island-give-name"), self::getCommands("island-give-description"), self::getCommands("island-give-usage"), self::getCommands("island-give-aliases")),
                    new IslandInfoCommand($this, self::getCommands("island-info-name"), self::getCommands("island-info-description"), self::getCommands("island-info-usage"), self::getCommands("island-info-aliases")),
                    new IslandWarpCommand($this, self::getCommands("island-warp-name"), self::getCommands("island-warp-description"), self::getCommands("island-warp-usage"), self::getCommands("island-warp-aliases")),
                    new IslandDelShareCommand($this, self::getCommands("island-delshare-name"), self::getCommands("island-delshare-description"), self::getCommands("island-delshare-usage"), self::getCommands("island-delshare-aliases")),
                    new SkylandAddShareCommand($this, self::getCommands("skyland-addshare-name"), self::getCommands("skyland-addshare-description"), self::getCommands("skyland-addshare-usage"), self::getCommands("skyland-addshare-aliases")),
                    new SkylandBuyCommand($this, self::getCommands("skyland-buy-name"), self::getCommands("skyland-buy-description"), self::getCommands("skyland-buy-usage"), self::getCommands("skyland-buy-aliases")),
                    new SkylandGiveCommand($this, self::getCommands("skyland-give-name"), self::getCommands("skyland-give-description"), self::getCommands("skyland-give-usage"), self::getCommands("skyland-give-aliases")),
                    new SkylandInfoCommand($this, self::getCommands("skyland-info-name"), self::getCommands("skyland-info-description"), self::getCommands("skyland-info-usage"), self::getCommands("skyland-info-aliases")),
                    new SkylandWarpCommand($this, self::getCommands("skyland-warp-name"), self::getCommands("skyland-warp-description"), self::getCommands("skyland-warp-usage"), self::getCommands("skyland-warp-aliases")),
                    new SkylandDelShareCommand($this, self::getCommands("skyland-delshare-name"), self::getCommands("skyland-delshare-description"), self::getCommands("skyland-delshare-usage"), self::getCommands("skyland-delshare-aliases")),
                    new LandAddShareCommand($this, self::getCommands("land-addshare-name"), self::getCommands("land-addshare-description"), self::getCommands("land-addshare-usage"), self::getCommands("land-addshare-aliases")),
                    new LandBuyCommand($this, self::getCommands("land-buy-name"), self::getCommands("land-buy-description"), self::getCommands("land-buy-usage"), self::getCommands("land-buy-aliases")),
                    new LandGiveCommand($this, self::getCommands("land-give-name"), self::getCommands("land-give-description"), self::getCommands("land-give-usage"), self::getCommands("land-give-aliases")),
                    new LandInfoCommand($this, self::getCommands("land-info-name"), self::getCommands("land-info-description"), self::getCommands("land-info-usage"), self::getCommands("land-info-aliases")),
                    new LandWarpCommand($this, self::getCommands("land-warp-name"), self::getCommands("land-warp-description"), self::getCommands("land-warp-usage"), self::getCommands("land-warp-aliases")),
                    new LandDelShareCommand($this, self::getCommands("land-delshare-name"), self::getCommands("land-delshare-description"), self::getCommands("land-delshare-usage"), self::getCommands("land-delshare-aliases")),
                    new FieldRegisteredList($this, self::getCommands("field-registeredlist-name"), self::getCommands("field-registeredlist-description"), self::getCommands("field-registeredlist-usage"), self::getCommands("field-registeredlist-aliases")),
                    new LandMakeCommand($this, self::getCommands("land-make-name"), self::getCommands("land-make-description"), self::getCommands("land-make-usage"), self::getCommands("land-make-aliases")),
                    new setProtectWorldCommand($this, self::getCommands("protectworld-set-name"), self::getCommands("protectworld-set-description"), self::getCommands("protectworld-set-usage"), self::getCommands("protectworld-set-aliases"))
                    ]);
        }

        /**
         * @param string $key
         * @param array ...$args
         * @return null|string
         */
        public static function get(string $key, bool $prefix = \true, array... $args): ?string{
            if(! self::$langcf->exists("message-".$key)) return \null;
            $st = self::$langcf->get("message-".$key);
            /** @var array $arg */
            foreach ($args as $arg) {
                $st = str_replace($arg[0], $arg[1], $st);
            }
            $pr = ($prefix)? TextFormat::BOLD . self::$langcf->get("Prefix") : "";
            return $pr.TextFormat::RESET.$st;
        }

        /**
         * @param string $key
         * @return mixed|null
         */
        public static function getCommands(string $key){
            if(! self::$langcf->exists("commands-".$key)) return \null;
            return self::$langcf->get("commands-".$key);
        }

        /**
         * @return FieldLoader
         */
        public function getFieldloader(): FieldLoader {
            return $this->fieldloader;
        }

        /**
         * @return IslandLoader
         */
        public function getIslandloader(): IslandLoader {
            return $this->islandloader;
        }

        /**
         * @return SkylandLoader
         */
        public function getSkylandloader(): SkylandLoader {
            return $this->skylandloader;
        }
    }