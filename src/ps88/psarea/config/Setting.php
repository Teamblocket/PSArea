<?php
    namespace ps88\psarea\config;

    use pocketmine\Server;
    use pocketmine\utils\Config;

    class Setting {

        /** @var Config */
        public static $setting;

        public static function makeConfig() {
            self::$setting = new Config(Server::getInstance()->getPluginManager()->getPlugin("PSArea")->getDataFolder() . "setting.yml", Config::YAML, [
                    "lang" => "eng",
                    "needidargs" => \false,
                    "prices" => [
                            "field" => 30000,
                            "island" => 30000,
                            "skyland" => 30000,
                            "land" => 10
                    ]
            ]);
        }
    }