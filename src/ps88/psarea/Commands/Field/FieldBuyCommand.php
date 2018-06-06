<?php
    namespace ps88\psarea\Commands\Field;

    use nlog\StormCore\StormPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Events\LandBuyEvent;
    use ps88\psarea\Loaders\Field\fieldloader;
    use ps88\psarea\MoneyTranslate\MoneyTranslator;
    use ps88\psarea\PSAreaMain;

    class FieldBuyCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * FieldBuyCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "buyfield", string $description = "Buy field", string $usageMessage = "/buyfield [id]", $aliases = ['Id']) {
            parent::__construct($name, $description, $usageMessage, $aliases);
            $this->owner = $owner;
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
                $sender->sendMessage(PSAreaMain::get("only-player"));
                return \true;
            }
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            if (($a = $this->owner->fieldloader->getAreaById($args[0])) == \null) {
                $sender->sendMessage(PSAreaMain::get("doesnt-exist"));
                return \true;
            }
            if ($a->owner !== \null) {
                $sender->sendMessage(PSAreaMain::get("owner-exist"));
                return \true;
            }
            if (count($this->owner->fieldloader->getAreasByOwner($sender->getName())) >= fieldloader::Maximum_Lands) {
                $sender->sendMessage(PSAreaMain::get("you-have-max", \true, ["@type", "field"]));
                return \true;
            }
            if (MoneyTranslator::getInstance()->getMoney($sender) < FieldLoader::$Land_Price) {
                $sender->sendMessage(PSAreaMain::get("you-need-money", \true, ["@money", FieldLoader::$Land_Price]));
                return \true;
            }
            Server::getInstance()->getPluginManager()->callEvent($ev = new LandBuyEvent($a, $sender));
            if ($ev->isCancelled()) {
                $sender->sendMessage(PSAreaMain::get("cancelled"));
                return \true;
            }
            $a->setOwner($sender);
            MoneyTranslator::getInstance()->reduceMoney($sender, FieldLoader::$Land_Price);
            $sender->sendMessage(PSAreaMain::get("you-bought", \true, ["@landnum", $a->getLandnum()], ["@type", "field"]));
            $nm = MoneyTranslator::getInstance()->getMoney($sender);
            $sender->sendMessage(PSAreaMain::get("your-money-now", \true, ["@money", $nm]));
            return \true;
        }
    }