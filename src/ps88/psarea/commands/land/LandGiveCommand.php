<?php
    namespace ps88\psarea\commands\land;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\loaders\land\LandLoader;

    use ps88\psarea\translator\Translator;

    class LandGiveCommand extends Command {




        /**
         * LandGiveCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "giveland", string $description = "Give Land to other Player", string $usageMessage = "/giveland [player] [id]", $aliases = ['Player', 'Id']) {
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
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $pl = Server::getInstance()->getPlayer($args[0]);
            if ($pl == \null) {
                $sender->sendMessage(Translator::get("doesnt-exist"));
                return \true;
            }
            if (count(LoaderManager::$landloader->getAreasByOwner($pl->getName())) >= LandLoader::Maximum_Lands) {
                $sender->sendMessage(Translator::get("you-have-max", \true, ["@type", "land"]));
                return \true;
            }
            $a->setOwner($pl);
            $sender->sendMessage(Translator::get("owner-changed"));
            $pl->sendMessage(Translator::get("you-got", \true, ["@landnum", $a->getLandnum()], ["@player", $sender->getName()], ["@type", "field"]));
        }
    }