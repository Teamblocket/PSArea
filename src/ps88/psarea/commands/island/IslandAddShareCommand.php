<?php
    namespace ps88\psarea\commands\island;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;


    use ps88\psarea\translator\Translator;

    class IslandAddShareCommand extends Command {




        /**
         * islandAddShareCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "addislandshare", string $description = "Add Island Shared Player", string $usageMessage = "/addislandshare [player] [id]", $aliases = ['Player', 'Id']) {
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
            $a = (!isset($args[1])) ? LoaderManager::$islandloader->getAreaByVector3($sender) : LoaderManager::$islandloader->getAreaById($args[1]);
            if ($a == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            if ($a->owner == \null) {
                $sender->sendMessage(Translator::get("not-yours", \true, ["@type", "island"]));
                return \true;
            }
            if ($a->owner->getName() !== $sender->getName()) {
                $sender->sendMessage(Translator::get("not-yours", \true, ["@type", "island"]));
                return \true;
            }
            if (!isset($args[1])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $pl = Server::getInstance()->getPlayer($args[0]);
            if ($pl == \null) {
                $sender->sendMessage(Translator::get("doesnt-exist"));
                return \true;
            }
            $a->addShare($pl);
            $sender->sendMessage(Translator::get("you-add-share", \true, ["@player", $pl->getName()], ["@landnum", $a->getLandnum()], ["@type", "island"]));
            return \true;
        }
    }