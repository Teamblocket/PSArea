<?php
    namespace ps88\psarea\Tasks;

    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\scheduler\PluginTask;
    use ps88\psarea\Loaders\Field\FieldArea;
    use ps88\psarea\Loaders\Field\FieldLoader;
    use ps88\psarea\PSAreaMain;

    class FieldAutoAddTask extends PluginTask {

        /** @var PSAreaMain */
        protected $owner;

        private $x = \null;

        private $z = \null;

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
            $xz = 7 + FieldLoader::$diagonalcount * 37;
            $x = $this->x ?? $xz;
            $z = $this->z ?? $xz;
            $i = 0;
            if($x > 7) {
                while (\true) {
                    $this->addArea($x, $xz);
                    $x = $x - 37;
                    $i++;
                    if($i % 20 == 0){
                        $this->x = $x;
                        $this->z = $z;
                        $this->i = $i;
                        return;
                    }
                    if ($x <= 7) break;
                }
            }
            if($z > 7) {
                while (\true) {
                    $this->addArea($xz, $z);
                    $z = $z - 37;
                    $i++;
                    if($i % 20 == 0){
                        $this->x = $x;
                        $this->z = $z;
                        $this->i = $i;
                        return;
                    }
                    if ($z <= 7) break;
                }
            }
            FieldLoader::$diagonalcount++;
            if((FieldLoader::$diagonalcount % 30) == 0){
                $this->getHandler()->setNextRun(FieldLoader::$diagonalcount / 20 );
            }
            $this->x = \null;
            $this->z = \null;
            $this->i = 0;
        }

        public function addArea($x, $z) {
            if (($x - 7) % 37 == 0 and ($z - 7) % 37 == 0) {
                if ($this->owner->fieldloader->getAreaByVector3(new Vector3($x, 0, $z))) return;
                $this->owner->fieldloader->addArea(new FieldArea(FieldLoader::$landcount++, new Vector2($x, $z), new Vector2($x + 29, $z + 29)));
            }
        }
    }