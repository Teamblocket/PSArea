<?php
    namespace ps88\psarea\translator;

    use pocketmine\Server;
    use pocketmine\utils\Config;
    use pocketmine\utils\TextFormat;
    use ps88\psarea\config\Setting;
    use ps88\psarea\PSAreaMain;

    class Translator {
        /** @var Config */
        public static $langcf;

        public static function run() {
            $p = Server::getInstance()->getPluginManager()->getPlugin("PSArea");
            if (!$p instanceof PSAreaMain) return;
            $lang = Setting::$setting->get("lang");
            if (!file_exists($p->getDataFolder() . "lang_{$lang}.yml")) {
                file_put_contents($p->getDataFolder() . "lang_{$lang}.yml", stream_get_contents($p->getResource("lang_{$lang}.yml")));
            }
            self::$langcf = new Config($p->getDataFolder() . "lang_{$lang}.yml", Config::YAML);
            if (!Setting::$setting->get("needidargs")) {
                self::$langcf->set("skyland-buy-usage", explode(" ", self::getCommands("skyland-buy-usage"))[0]);
                self::$langcf->set("skyland-buy-aliases", []);
                self::$langcf->set("island-buy-usage", explode(" ", self::getCommands("island-buy-usage"))[0]);
                self::$langcf->set("island-buy-aliases", []);
            }
        }


        /**
         * @param string $key
         * @param array ...$args
         * @return null|string
         */
        public static function get(string $key, bool $prefix = \true, array... $args): ?string {
            if (!self::$langcf->exists("message-" . $key)) return \null;
            $st = self::$langcf->get("message-" . $key);
            /** @var array $arg */
            foreach ($args as $arg) {
                $st = str_replace($arg[0], $arg[1], $st);
            }
            $pr = ($prefix) ? TextFormat::BOLD . self::$langcf->get("Prefix") : "";
            return $pr . TextFormat::RESET . $st;
        }

        /**
         * @param string $key
         * @return mixed|null
         */
        public static function getCommands(string $key) {
            if (!self::$langcf->exists("commands-" . $key)) return \null;
            return self::$langcf->get("commands-" . $key);
        }
    }