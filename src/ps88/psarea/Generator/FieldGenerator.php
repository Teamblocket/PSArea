<?php
    namespace ps88\psarea\Generator {

        /*
         * 이 코드는 SOLOLand(Nukkit) 에서 가져왔으며
         * PS88이 java에서 php로 번역하였음을 알려드립니다.
         */

        use pocketmine\level\generator\Generator;
        use pocketmine\block\Block;
        use pocketmine\block\Stone as BlockStone;
        use pocketmine\level\ChunkManager;
        use pocketmine\math\Vector3;
        use pocketmine\utils\Random;

        class FieldGenerator extends Generator {

            //public static TYPE_GRID_LAND = 11;


            private $options = [];
            private $floorLevel;

            private $flatBlocks = [[Block::BEDROCK, 0], [Block::STONE, 0], [Block::STONE, 0], [Block::STONE, 0], [Block::STONE, 0], [Block::DIRT, 0], [Block::DIRT, 0], [Block::DIRT, 0]];
            private $roadBlock = [Block::STONE, BlockStone::POLISHED_ANDESITE];
            private $roadWidth = 5;
            private $roadDepth = 5;

            private $landBlock = [Block::GRASS, 0];
            private $landWidth = 32;
            private $landDepth = 32;
            private $landBorderBlock = [168, 0]; //Block::DOUBLE_SLAB; //..?

            public function getChunkManager(): ChunkManager {
                return $this->level;
            }

            public function getSettings(): array {
                return $this->options;
            }

            public function getName(): string {
                return "field";
            }

            public function __construct(array $options = []) {

            }

            public function getLandWidth(): int {
                return $this->landWidth;
            }

            public function getLandDepth(): int {
                return $this->landDepth;
            }

            public function getRoadWidth(): int {
                return $this->roadWidth;
            }

            public function getRoadDepth(): int {
                return $this->roadDepth;
            }

            public function generateChunk(int $chunkX, int $chunkZ): void {
                $chunk = $this->level->getChunk($chunkX, $chunkZ);
                if ($chunkX >= 0 && $chunkZ >= 0) {
                    for ($x = 0; $x <= 15; $x++) {
                        for ($z = 0; $z <= 15; $z++) {
                            $y = 0;
                            foreach ($this->flatBlocks as $flatBlock) {
                                $chunk->setBlock($x, $y++, $z, ...$flatBlock);
                            }
                            $chunk->setBlock($x, $y, $z, ...$this->calcGen($chunkX * 16 + $x, $chunkZ * 16 + $z));
                        }
                    }
                }
                $this->level->setChunk($chunkX, $chunkZ, $chunk);
            }

            private function calcGen(int $worldX, int $worldZ) {
                if ($worldX == 0 || $worldZ == 0) {
                    return $this->landBorderBlock;
                }
                $gridlandX = $worldX % ($this->landWidth + $this->roadWidth);
                $gridlandZ = $worldZ % ($this->landDepth + $this->roadDepth);

                if ($gridlandX >= ($this->roadWidth + 2) && $gridlandZ >= ($this->roadDepth + 2)) {
                    return $this->landBlock;
                }
                if ($gridlandX >= ($this->roadWidth + 1) && $gridlandZ >= ($this->roadDepth + 1)) {
                    return $this->landBorderBlock;
                }
                if ($gridlandX >= 1 && $gridlandZ >= 1) {
                    return $this->roadBlock;
                }
                if ($gridlandX == 0 && $gridlandZ >= $this->roadDepth + 1) {
                    return $this->landBorderBlock;
                }
                if ($gridlandZ == 0 && $gridlandX >= $this->roadWidth + 1) {
                    return $this->landBorderBlock;
                }
                if ($gridlandX == 0 && $gridlandZ == 0) {
                    return $this->landBorderBlock;
                }
                return $this->roadBlock;
            }

            public function populateChunk(int $chunkX, int $chunkZ): void {
            }

            public function getSpawn(): Vector3 {
                return new Vector3(128, $this->floorLevel, 128);
            }
        }
    }

