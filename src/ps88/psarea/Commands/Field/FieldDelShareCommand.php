<?php
    namespace ps88\psarea\Commands\Field;

    use nlog\StormCore\StormPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    use pocketmine\Server;
    use ps88\psarea\Loaders\Field\fieldloader;
    use ps88\psarea\PSAreaMain;

    class FieldDelShareCommand extends Command {

        /** @var PSAreaMain */
        private $owner;

        /**
         * FieldAddShareCommand constructor.
         * @param string $name
         * @param PSAreaMain $owner
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(PSAreaMain $owner, string $name = "delfieldshare", string $description = "Delete Field Shared Player", string $usageMessage = "/delfieldshare [player] [id]", $aliases = ['Player', 'Id']) {
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
                $sender->sendMessage("Only Player Can see this.");
                return \true;
            }
            $a = (!isset($args[1])) ? $this->owner->fieldloader->getAreaByVector3($sender) : $this->owner->fieldloader->getAreaById($args[1]);
            if ($a == \null) {
                $sender->sendMessage("Not Registered");
                return \true;
            }
            if ($a->owner == \null) {
                $sender->sendMessage("It's not your Field");
                return \true;
            }
            if ($a->owner->getName() !== $sender->getName()) {
                $sender->sendMessage("It's not your Field");
                return \true;
            }
            if (!isset($args[1])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $pl = Server::getInstance()->getPlayer($args[0]);
            if ($pl == \null) {
                $sender->sendMessage("Doesn't exist");
                return \true;
            }
            $a->delShare($pl);
            $sender->sendMessage("You del {$pl->getName()} at {$a->getLandnum()} Field");
            return \true;
        }
    }