<?php
    namespace ps88\psarea\generator;

    use ps88\psarea\object\Sphere;
    use pocketmine\math\Vector3;
    use pocketmine\level\generator\Generator;
    use pocketmine\level\generator\object\{
            Tree, TallGrass
    };

    class SkylandGenerator extends Generator {

        public function __construct(array $options = []) {
            //parent::__construct($options);
        }

        public function getSettings(): array {
            return [];
        }

        public function getName(): string {
            return "skyland";
        }

        public function generateChunk(int $chunkX, int $chunkZ): void {
            $chunk = $this->level->getChunk($chunkX, $chunkZ);

            if ($chunkX > 0 and $chunkZ > 0) {
                $islandX = ($chunkX * 16) % 200;
                $islandZ = ($chunkZ * 16) % 200;
                if ($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15) {
                    foreach (Sphere::getElements(8, 7, 8, 7) as $el) {
                        list($x, $y, $z) = $el;

                        if ($y < 0) {
                            continue;
                        } else if ($y < 10) {
                            $chunk->setBlock($x, $y, $z, 1);
                        } else if ($y < 12) {
                            $chunk->setBlock($x, $y, $z, 2);
                        }
                    }
                }
            }
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }

        public function populateChunk($chunkX, $chunkZ): void {
            if ($chunkX > 0 and $chunkZ > 0) {
                $islandX = ($chunkX * 16) % 200;
                $islandZ = ($chunkZ * 16) % 200;
                if ($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15) {
                    $chunk = $this->level->getChunk($chunkX, $chunkZ);

                    $x = $chunkX * 16 + 8;
                    $z = $chunkZ * 16 + 8;
                    $y = $chunk->getHighestBlockAt(8, 8);
                    Tree::growTree($this->level, $x, $y + 1, $z, $this->random);

                    TallGrass::growGrass($this->level, new Vector3($x, $y, $z), $this->random);
                }
            }
        }

        public function getSpawn(): Vector3 {
            return new Vector3(100, 25, 100);
        }
    }