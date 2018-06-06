<?php
    namespace ps88\psarea\Tasks;

    use pocketmine\math\Vector2;
    use pocketmine\scheduler\PluginTask;
    use ps88\psarea\Loaders\Field\FieldLoader;
    use ps88\psarea\Loaders\Island\IslandArea;
    use ps88\psarea\Loaders\Island\IslandLoader;
    use ps88\psarea\Loaders\Skyland\SkylandArea;
    use ps88\psarea\Loaders\Skyland\SkylandLoader;
    use ps88\psarea\PSAreaMain;

    class AreaAddTask extends PluginTask {

        /** @var PSAreaMain */
        protected $owner;

        public function __construct(PSAreaMain $owner) {
            parent::__construct($owner);
        }

        /**
         * Actions to execute when run
         *
         * @param int $currentTick
         *
         * @return void
         */
        public function onRun(int $currentTick) {
            //104 + 200씩(x), 104(z)
            $id = IslandLoader::$landcount++;
            $this->owner->getIslandloader()->addArea(new IslandArea($id, new Vector2(104 + $id * 200, 104)));
            $id = SkylandLoader::$landcount++;
            $this->owner->getSkylandloader()->addArea(new SkylandArea($id, new Vector2(104 + $id * 200, 104)));
        }
    }