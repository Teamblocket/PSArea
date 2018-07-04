<?php
    namespace ps88\psarea;


    use pocketmine\plugin\PluginBase;
    use ps88\psarea\commands\field\SetWelcomeMessageCommand;
    use ps88\psarea\config\Setting;
    use ps88\psarea\listeners\WelcomeListener;
    use ps88\psarea\loaders\LoaderManager;
    use ps88\psarea\moneytranslate\MoneyTranslator;
    use ps88\psarea\worldmanager\ProtectWorld;
    use ps88\psarea\translator\Translator;

    class PSAreaMain extends PluginBase{

        /** @var ProtectWorld */
        public $protectworld;

        protected function onEnable(): void {
            @mkdir($this->getDataFolder());
            Setting::makeConfig();
            $this->protectworld = new ProtectWorld($this);
            $this->getServer()->getCommandMap()->register("PSArea", new SetWelcomeMessageCommand(Translator::getCommands("set-wel-name"), Translator::get("set-wel-description"), Translator::get("set-wel-usage"), Translator::getCommands("set-wel-aliases")));
            Translator::run();
            MoneyTranslator::run();
            LoaderManager::Load();
            $this->getServer()->getPluginManager()->registerEvents(new WelcomeListener(), $this);
        }

        protected function onDisable(): void {
            Setting::$setting->save();
            Translator::$langcf->save();
            LoaderManager::saveAll();
        }
    }