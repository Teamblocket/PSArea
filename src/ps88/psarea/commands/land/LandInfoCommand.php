<?php
    namespace ps88\psarea\commands\land;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;


    use ps88\psarea\translator\Translator;

    class LandInfoCommand extends Command {




        /**
         * LandInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "infoland", string $description = "Fet Land info", string $usageMessage = "/infoland [id]", $aliases = ['Id']) {
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
            $a = (!isset($args[0])) ? LoaderManager::$landloader->getAreaByPosition($sender) : LoaderManager::$landloader->getAreaById($args[0]);
            if ($a == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            $sender->sendMessage(Translator::get("info-start", \true, ["@landnum", $a->getLandnum()], ["@type", "land"]));
            $owner = ($a->owner == \null) ? Translator::get("none") : $a->owner->getName();
            $sender->sendMessage(Translator::get("owner", \true, ["@owner", $owner]));
            $sender->sendMessage(Translator::get("shares"));
            if (empty($a->getShares())) {
                $sender->sendMessage(Translator::get("none"));
            } else {
                foreach ($a->getShares() as $share) {
                    $sender->sendMessage($share->getName());
                }
            }
            return \true;
        }
    }