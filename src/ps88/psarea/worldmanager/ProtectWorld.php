<?php
    namespace ps88\psarea\worldmanager;

    use pocketmine\level\Level;
    use pocketmine\Server;
    use ps88\psarea\commands\worldmanager\setProtectWorldCommand;
    use ps88\psarea\PSAreaMain;
    use ps88\psarea\listeners\ProtectWorldListener;
    use ps88\psarea\translator\Translator;

    class ProtectWorld {
        /** @var ProtectWorld */
        private static $instance;

        /** @var array */
        public $levels = [];

        /** @var PSAreaMain */
        public $main;

        public function __construct(PSAreaMain $main) {
            Server::getInstance()->getPluginManager()->registerEvents(new ProtectWorldListener(), $main);
            $this->main = $main;
            self::$instance = $this;
            Server::getInstance()->getCommandMap()->registerAll('PSArea', [
                    new setProtectWorldCommand( Translator::getCommands("protectworld-set-name"), Translator::getCommands("protectworld-set-description"), Translator::getCommands("protectworld-set-usage"), Translator::getCommands("protectworld-set-aliases"))
            ]);
        }

        public function isLevelProtected(Level $level): bool {
            if (!isset($this->levels[$level->getId()])) return \true;
            return $this->levels[$level->getId()];
        }

        public function setLevelProtect(Level $level, bool $isProtected): void {
            $this->levels[$level->getId()] = $isProtected;
        }

        public static function getInstance(): self {
            return self::$instance;
        }
    }