<?php
    namespace ps88\psarea\commands\worldmanager;

    use pocketmine\command\Command;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\worldmanager\ProtectWorld;

    use ps88\psarea\translator\Translator;

    class setProtectWorldCommand extends Command {



        /**
         * IslandInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "setprotectworld", string $description = "Set Protected World", string $usageMessage = "/setprotectworld [level] [isProtect(true)]", $aliases = ['level', 'isProtect']) {
            parent::__construct($name, $description, $usageMessage, $aliases);

        }

        /**
         * @param CommandSender $sender
         * @param string $commandLabel
         * @param string[] $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
            if (!$sender instanceof Player) {
                $sender->sendMessage(Translator::get("only-player"));
                return \true;
            }
            if (!$sender->isOp()) { //TODO Remove this
                $sender->sendMessage(Translator::get("no-permission"));
                return true;
            }
            $level = (!isset($args[0])) ? $sender->getLevel() : Server::getInstance()->getLevelByName($args[0]);
            if ($level == \null) {
                $sender->sendMessage(Translator::get("cant-find-level"));
                return \true;
            }
            $b = (!isset($args[1]) or $args[1] == "true") ? \true : \false;
            ProtectWorld::getInstance()->setLevelProtect($level, $b);
            $sender->sendMessage(Translator::get("world-will-be-protected", \true, ["@level", $level->getName()]));
            return \true;
        }
    }
