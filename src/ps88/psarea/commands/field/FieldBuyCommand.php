<?php
    namespace ps88\psarea\commands\field;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\events\area\PSAreaBuyEvent;

    use ps88\psarea\moneytranslate\MoneyTranslator;

    use ps88\psarea\translator\Translator;

    class FieldBuyCommand extends Command {


        /**
         * FieldBuyCommand constructor.
         * @param string $name
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "buyfield", string $description = "Buy field", string $usageMessage = "/buyfield [id]", $aliases = ['Id']) {
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
            if (($a = LoaderManager::$fieldloader->getAreaById($args[0])) == \null) {
                $sender->sendMessage(Translator::get("doesnt-exist"));
                return \true;
            }
            if ($a->owner !== \null) {
                $sender->sendMessage(Translator::get("owner-exist"));
                return \true;
            }
            if (count(LoaderManager::$fieldloader->getAreasByOwner($sender->getName())) >= fieldloader::Maximum_Lands) {
                $sender->sendMessage(Translator::get("you-have-max", \true, ["@type", "field"]));
                return \true;
            }
            if (MoneyTranslator::getInstance()->getMoney($sender) < FieldLoader::$Land_Price) {
                $sender->sendMessage(Translator::get("you-need-money", \true, ["@money", FieldLoader::$Land_Price]));
                return \true;
            }
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaBuyEvent($a, $sender));
            if ($ev->isCancelled()) {
                $sender->sendMessage(Translator::get("cancelled"));
                return \true;
            }
            $a->setOwner($sender);
            MoneyTranslator::getInstance()->reduceMoney($sender, FieldLoader::$Land_Price);
            $sender->sendMessage(Translator::get("you-bought", \true, ["@landnum", $a->getLandnum()], ["@type", "field"]));
            $nm = MoneyTranslator::getInstance()->getMoney($sender);
            $sender->sendMessage(Translator::get("your-money-now", \true, ["@money", $nm]));
            return \true;
        }
    }