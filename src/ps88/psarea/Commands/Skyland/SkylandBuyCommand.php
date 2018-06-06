<?php
    namespace ps88\psarea\Commands\Skyland;

    use pocketmine\Player;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Server;
    use ps88\psarea\Events\LandBuyEvent;
    use ps88\psarea\Loaders\Skyland\SkylandLoader;
    use ps88\psarea\MoneyTranslate\MoneyTranslator;
    use ps88\psarea\PSAreaMain;

    class SkylandBuyCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * SkylandBuyCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "buyskyland", string $description = "Buy skyland", string $usageMessage = "/buyskyland [id]", $aliases = ['Id']) {
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
            if (!isset($args[0]) and $this->owner->setting->get("needidargs")) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $args[0] = (! $this->owner->setting->get("needidargs"))? SkylandLoader::$landcount++ : $args[0];
            if(! isset($args[0])){
                $sender->sendMessage($this->getUsage());
            }
            if (($a = $this->owner->skylandloader->getAreaById($args[0])) == \null) {
                $sender->sendMessage(PSAreaMain::get("doesnt-exist"));
                return \true;
            }
            if ($a->owner !== \null) {
                $sender->sendMessage(PSAreaMain::get("owner-exist"));
                return \true;
            }
            if (count($this->owner->skylandloader->getAreasByOwner($sender->getName())) >= SkylandLoader::Maximum_Lands) {
                $sender->sendMessage(PSAreaMain::get("you-have-max", \true, ["@type", "skyland"]));
                return \true;
            }
            if (MoneyTranslator::getInstance()->getMoney($sender) < SkylandLoader::$Land_Price) {
                $sender->sendMessage(PSAreaMain::get("you-need-money", \true, ["@money", SkylandLoader::$Land_Price]));
                return \true;
            }
            Server::getInstance()->getPluginManager()->callEvent($ev = new LandBuyEvent($a, $sender));
            if ($ev->isCancelled()) {
                $sender->sendMessage(PSAreaMain::get("cancelled"));
                return \true;
            }
            $a->setOwner($sender);
            MoneyTranslator::getInstance()->reduceMoney($sender, SkylandLoader::$Land_Price);
            $sender->sendMessage(PSAreaMain::get("you-bought", \true, ["@landnum", $a->getLandnum()], ["@type", "skyland"]));
            $nm = MoneyTranslator::getInstance()->getMoney($sender);
            $sender->sendMessage(PSAreaMain::get("your-money-now", \true, ["@money", $nm]));
            return \true;
        }
    }
