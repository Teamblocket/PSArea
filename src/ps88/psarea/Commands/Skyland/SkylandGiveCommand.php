<?php
    namespace ps88\psarea\Commands\Skyland;

    use nlog\StormCore\StormPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Loaders\Skyland\SkylandLoader;
    use ps88\psarea\PSAreaMain;

    class SkylandGiveCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * SkylandGiveCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "giveskyland", string $description = "Give Skyland to other Player", string $usageMessage = "/giveskyland [player] [id]", $aliases = ['Player', 'Id']) {
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
            $a = (!isset($args[1])) ? $this->owner->skylandloader->getAreaByVector3($sender) : $this->owner->skylandloader->getAreaById($args[1]);
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
            if (count($this->owner->skylandloader->getAreasByOwner($pl->getName())) >= SkylandLoader::Maximum_Lands) {
                $sender->sendMessage(PSAreaMain::get("you-have-max", \true, ["@type", "skyland"]));
                return \true;
            }
            $a->setOwner($pl);
            $sender->sendMessage(PSAreaMain::get("owner-changed"));
            $pl->sendMessage(PSAreaMain::get("you-got", \true, ["@landnum", $a->getLandnum()], ["@player", $sender->getName()], ["@type", "field"]));
        }
    }