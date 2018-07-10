<?php
    namespace ps88\psarea\commands\land;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use ps88\psarea\loaders\land\LandLoader;

    use ps88\psarea\translator\Translator;

    class LandMakeCommand extends Command {




        /**
         * LandMakeCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "makeland", string $description = "Make land", string $usageMessage = "/makeland [id]", $aliases = ['Id']) {
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
            if ($sender->getLevel()->getName() == "land" or $sender->getLevel()->getName() == "skyland" or $sender->getLevel()->getName() == "field") {
                $sender->sendMessage(Translator::get("land-cant-make-at", \true, ["@level", $sender->getLevel()->getName()]));
                return \true;
            }
            $idd = LandLoader::$landcount;
            $sender->sendMessage(Translator::get("land-start-making", \true, ["@landnum", $idd]));
            LoaderManager::$landloader->startRegister($sender, LandLoader::$landcount++, $sender->getLevel());
            $sender->sendMessage(Translator::get("touch-2"));
            return \true;
        }
    }