<?php
    namespace ps88\psarea\commands\land;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;


    use ps88\psarea\translator\Translator;

    class LandWarpCommand extends Command {




        /**
         * LandInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "warpland", string $description = "Warp to land", string $usageMessage = "/warpland [id]", $aliases = ['Id']) {
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
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $id = (int) $args[0];
            if (($a = LoaderManager::$landloader->getAreaById($id)) == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            if (!$a->Warp($sender)) {
                $sender->sendMessage(Translator::get("cancelled"));
                return \true;
            }
            $sender->sendMessage(Translator::get("warp-to", \true, ["@landnum", $id], ["@type", "land"]));
            return \true;
        }
    }