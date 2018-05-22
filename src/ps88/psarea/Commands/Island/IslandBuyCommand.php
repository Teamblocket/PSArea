<?php
    namespace ps88\psarea\Commands\Island;

    use nlog\StormCore\StormPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Events\LandBuyEvent;
    use ps88\psarea\Loaders\Island\IslandLoader;
    use ps88\psarea\MoneyTranslate\MoneyTranslator;
    use ps88\psarea\PSAreaMain;

    class IslandBuyCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * IslandBuyCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "buyisland", string $description = "Buy Island", string $usageMessage = "/buyisland [id]", $aliases = ['Id']) {
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
            if (!isset($args[0]) and $this->owner->setting->get("needidargs")) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $args[0] = (! $this->owner->setting->get("needidargs"))? IslandLoader::$landcount++ : $args[0];
            if(! isset($args[0])){
                $sender->sendMessage($this->getUsage());
            }
            if (($a = $this->owner->islandloader->getAreaById($args[0])) == \null) {
                $sender->sendMessage(PSAreaMain::get("doesnt-exist"));
                return \true;
            }
            if ($a->owner !== \null) {
                $sender->sendMessage(PSAreaMain::get("owner-exist"));
                return \true;
            }
            if (count($this->owner->islandloader->getAreasByOwner($sender->getName())) >= IslandLoader::Maximum_Lands) {
                $sender->sendMessage(PSAreaMain::get("you-have-max", \true, ["@type", "island"]));
                return \true;
            }
            if (MoneyTranslator::getInstance()->getMoney($sender) < IslandLoader::Land_Price) {
                $sender->sendMessage(PSAreaMain::get("you-need-money", \true, ["@money", IslandLoader::Land_Price]));
                return \true;
            }
            Server::getInstance()->getPluginManager()->callEvent($ev = new LandBuyEvent($a, $sender));
            if ($ev->isCancelled()) {
                $sender->sendMessage(PSAreaMain::get("cancelled"));
                return \true;
            }
            $a->setOwner($sender);
            MoneyTranslator::getInstance()->reduceMoney($sender, IslandLoader::Land_Price);
            $sender->sendMessage(PSAreaMain::get("you-bought", \true, ["@landnum", $a->getLandnum()], ["@type", "island"]));
            $nm = MoneyTranslator::getInstance()->getMoney($sender);
            $sender->sendMessage(PSAreaMain::get("your-money-now", \true, ["@money", $nm]));
            return \true;
        }
    }