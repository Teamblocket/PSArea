<?php
    namespace ps88\psarea\commands\island;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\events\area\PSAreaBuyEvent;

    use ps88\psarea\moneytranslate\MoneyTranslator;

    use ps88\psarea\translator\Translator;

    class IslandBuyCommand extends Command {




        /**
         * IslandBuyCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "buyisland", string $description = "Buy Island", string $usageMessage = "/buyisland [id]", $aliases = ['Id']) {
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
            if (!isset($args[0]) and LoaderManager::$setting->get("needidargs")) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $args[0] = (!LoaderManager::$setting->get("needidargs")) ? IslandLoader::$landcount++ : $args[0];
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
            }
            if (($a = LoaderManager::$islandloader->getAreaById($args[0])) == \null) {
                $sender->sendMessage(Translator::get("doesnt-exist"));
                return \true;
            }
            if ($a->owner !== \null) {
                $sender->sendMessage(Translator::get("owner-exist"));
                return \true;
            }
            if (count(LoaderManager::$islandloader->getAreasByOwner($sender->getName())) >= IslandLoader::Maximum_Lands) {
                $sender->sendMessage(Translator::get("you-have-max", \true, ["@type", "island"]));
                return \true;
            }
            if (MoneyTranslator::getInstance()->getMoney($sender) < IslandLoader::$Land_Price) {
                $sender->sendMessage(Translator::get("you-need-money", \true, ["@money", IslandLoader::$Land_Price]));
                return \true;
            }
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaBuyEvent($a, $sender));
            if ($ev->isCancelled()) {
                $sender->sendMessage(Translator::get("cancelled"));
                return \true;
            }
            $a->setOwner($sender);
            MoneyTranslator::getInstance()->reduceMoney($sender, IslandLoader::$Land_Price);
            $sender->sendMessage(Translator::get("you-bought", \true, ["@landnum", $a->getLandnum()], ["@type", "island"]));
            $nm = MoneyTranslator::getInstance()->getMoney($sender);
            $sender->sendMessage(Translator::get("your-money-now", \true, ["@money", $nm]));
            return \true;
        }
    }