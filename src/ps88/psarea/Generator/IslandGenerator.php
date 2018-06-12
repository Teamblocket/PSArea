<?php
    namespace ps88\psarea\Generator;

    use ps88\psarea\Object\Sphere;
    use pocketmine\math\Vector3;
    use pocketmine\level\ChunkManager;
    use pocketmine\utils\Random;
    use pocketmine\level\generator\Generator;
    use pocketmine\level\generator\object\{
            Tree, TallGrass
    };

    class IslandGenerator extends Generator {

        public function __construct(array $options = []) {

        }

        public function getSettings(): array {
            return [];
        }

        public function getName(): string {
            return "island";
        }

        public function generateChunk(int $chunkX, int $chunkZ): void {
            $chunk = $this->level->getChunk($chunkX, $chunkZ);

            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    foreach ([7, 1, 1, 1, 1, 1, 1, 12, 8, 8] as $y => $blockId) {
                        $chunk->setBlock($x, $y, $z, $blockId);
                    }
                }
            }

            if ($chunkX > 0 and $chunkZ > 0) {
                $islandX = ($chunkX * 16) % 200;
                $islandZ = ($chunkZ * 16) % 200;
                if ($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15) {
                    foreach (Sphere::getElements(8, 7, 8, 7) as $el) {
                        list($x, $y, $z) = $el;

                        if ($y < 7) {
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

?>
