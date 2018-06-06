<?php
    namespace ps88\psarea\Tasks;

    use pocketmine\math\Vector2;
    use pocketmine\math\Vector3;
    use pocketmine\scheduler\AsyncTask;
    use pocketmine\scheduler\Task;
    use ps88\psarea\Loaders\Field\FieldArea;
    use ps88\psarea\Loaders\Field\FieldLoader;
    use ps88\psarea\Loaders\Island\IslandArea;
    use ps88\psarea\Loaders\Island\IslandLoader;
    use ps88\psarea\Loaders\Skyland\SkylandArea;
    use ps88\psarea\Loaders\Skyland\SkylandLoader;
    use ps88\psarea\PSAreaMain;

    class AreaAddTask extends Task {

        /** @var IslandLoader */
        private $isloader;

        /** @var SkylandLoader */
        private $skyloader;

        /** @var FieldLoader */
        private $filoader;

        private $x = \null;

        private $z = \null;

        public function __construct(IslandLoader $islandLoader, SkylandLoader $skylandLoader, FieldLoader $fieldLoader) {
            $this->isloader = $islandLoader;
            $this->skyloader = $skylandLoader;
            $this->filoader = $fieldLoader;
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
            $this->RunLands();
            $this->RunField();
        }

        private function RunLands(){
            $id = IslandLoader::$landcount++;
            $this->isloader->addArea(new IslandArea($id, new Vector2(104 + $id * 200, 104)));
            $id = SkylandLoader::$landcount++;
            $this->skyloader->addArea(new SkylandArea($id, new Vector2(104 + $id * 200, 104)));
        }
        private function RunField() {
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
                        return;
                    }
                    if ($z <= 7) break;
                }
            }
            FieldLoader::$diagonalcount++;
            $this->x = \null;
            $this->z = \null;
        }

        private function addArea($x, $z) {
            if (($x - 7) % 37 == 0 and ($z - 7) % 37 == 0) {
                if ($this->filoader->getAreaByVector3(new Vector3($x, 0, $z))) return;
                $this->filoader->addArea(new FieldArea(FieldLoader::$landcount++, new Vector2($x, $z), new Vector2($x + 29, $z + 29)));
            }
        }
    }