<?php
    namespace ps88\psarea\Commands\Island;

    use nlog\StormCore\StormPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Loaders\Island\IslandLoader;
    use ps88\psarea\PSAreaMain;

    class IslandGiveCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * IslandGiveCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "giveisland", string $description = "Give Island to other Player", string $usageMessage = "/giveisland [player] [id]", $aliases = ['Player', 'Id']) {
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
            $a = (!isset($args[1])) ? $this->owner->islandloader->getAreaByVector3($sender) : $this->owner->islandloader->getAreaById($args[1]);
            if ($a == \null) {
                $sender->sendMessage(PSAreaMain::get("not-registered"));
                return \true;
            }
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $pl = Server::getInstance()->getPlayer($args[0]);
            if ($pl == \null) {
                $sender->sendMessage(PSAreaMain::get("doesnt-exist"));
                return \true;
            }
            if (count($this->owner->islandloader->getAreasByOwner($pl->getName())) >= IslandLoader::Maximum_Lands) {
                $sender->sendMessage(PSAreaMain::get("you-have-max", \true, ["@type", "island"]));
                return \true;
            }
            $a->setOwner($pl);
            $sender->sendMessage(PSAreaMain::get("owner-changed"));
            $pl->sendMessage(PSAreaMain::get("you-got", \true, ["@landnum", $a->getLandnum()], ["@player", $sender->getName()], ["@type", "field"]));
        }
    }