<?php
    namespace ps88\psarea\commands\land;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;

    use ps88\psarea\translator\Translator;

    class LandDelShareCommand extends Command {




        /**
         * FieldAddShareCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "dellandshare", string $description = "Delete Land Shared Player", string $usageMessage = "/dellandshare [player] [id]", $aliases = ['Player', 'Id']) {
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
            $a = (!isset($args[1])) ? LoaderManager::$landloader->getAreaByPosition($sender) : LoaderManager::$landloader->getAreaById($args[1]);
            if ($a == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            if ($a->owner == \null) {
                $sender->sendMessage(Translator::get("not-yours", \true, ["@type", "land"]));
                return \true;
            }
            if ($a->owner->getName() !== $sender->getName()) {
                $sender->sendMessage(Translator::get("not-yours", \true, ["@type", "land"]));
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
            $a->delShare($pl);
            $sender->sendMessage("You del {$pl->getName()} at {$a->getLandnum()} land");
            return \true;
        }
    }