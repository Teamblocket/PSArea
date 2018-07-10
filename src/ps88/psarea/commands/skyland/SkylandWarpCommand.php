<?php
    namespace ps88\psarea\commands\skyland;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;


    use ps88\psarea\translator\Translator;

    class SkylandWarpCommand extends Command {




        /**
         * SkylandInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "warpskyland", string $description = "Warp to skyland", string $usageMessage = "/warpskyland [id]", $aliases = ['Id']) {
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
            if (($a = LoaderManager::$skylandloader->getAreaById($id)) == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            if (!$a->Warp($sender)) {
                $sender->sendMessage(Translator::get("cancelled"));
                return \true;
            }
            $sender->sendMessage(Translator::get("warp-to", \true, ["@landnum", $id], ["@type", "skyland"]));
            return \true;
        }
    }