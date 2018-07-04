<?php
    namespace ps88\psarea\commands\field;

    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;

    use ps88\psarea\translator\Translator;

    class FieldRegisteredList extends Command {



        /**
         * FieldInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct( string $name = "fieldlist", string $description = "field list", string $usageMessage = "/fieldlist", $aliases = []) {
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
            $s = Translator::get("field-list") . " : ";
            foreach (LoaderManager::$fieldloader->getAreas() as $area) {
                if ($area->owner == \null) continue;
                $s .= "[" . $area->getLandnum() . "]";
            }
            $sender->sendMessage($s);
            return \true;
        }
    }