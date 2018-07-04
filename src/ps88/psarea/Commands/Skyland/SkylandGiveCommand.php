<?php
    namespace ps88\psarea\commands\skyland;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\loaders\skyland\SkylandLoader;

    use ps88\psarea\translator\Translator;

    class SkylandGiveCommand extends Command {




        /**
         * SkylandGiveCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "giveskyland", string $description = "Give Skyland to other Player", string $usageMessage = "/giveskyland [player] [id]", $aliases = ['Player', 'Id']) {
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
            $a = (!isset($args[1])) ? LoaderManager::$skylandloader->getAreaByVector3($sender) : LoaderManager::$skylandloader->getAreaById($args[1]);
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
            if (count(LoaderManager::$skylandloader->getAreasByOwner($pl->getName())) >= SkylandLoader::Maximum_Lands) {
                $sender->sendMessage(Translator::get("you-have-max", \true, ["@type", "skyland"]));
                return \true;
            }
            $a->setOwner($pl);
            $sender->sendMessage(Translator::get("owner-changed"));
            $pl->sendMessage(Translator::get("you-got", \true, ["@landnum", $a->getLandnum()], ["@player", $sender->getName()], ["@type", "field"]));
        }
    }