<?php
    namespace ps88\psarea\commands\field;


    use pocketmine\command\Command;
    use ps88\psarea\loaders\LoaderManager;
    use pocketmine\command\commandsender;
    use pocketmine\Player;


    use ps88\psarea\translator\Translator;

    class SetWelcomeMessageCommand extends Command {


        /**
         * FieldInfoCommand constructor.
         * @param string $name
         *
         * @param string $description
         * @param string|null $usageMessage
         * @param array $aliases
         */
        public function __construct(string $name, string $description, string $usageMessage, array $aliases) {
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
            $a = LoaderManager::getArea($sender->asPosition());
            if ($a == \null) {
                $sender->sendMessage(Translator::get("not-registered"));
                return \true;
            }
            if($a->getOwner()->getName() !== $sender->getName()){
                $sender->sendMessage(Translator::get("not-yours", \true, ["@type", "Area"]));
                return \true;
            }
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return \true;
            }
            $a->setWelcomeMessage($args[0]);
            return \true;
        }
    }