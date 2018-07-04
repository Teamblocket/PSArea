<?php
    namespace ps88\psarea\listeners;

    use pocketmine\event\Listener;
    use pocketmine\event\player\PlayerInteractEvent;
    use pocketmine\level\Position;
    use pocketmine\math\Vector2;
    use ps88\psarea\loaders\land\LandArea;
    use ps88\psarea\loaders\LoaderManager;

    class LandListener implements Listener {

        public function get(PlayerInteractEvent $ev) {
            $pl = $ev->getPlayer();
            $bl = $ev->getTouchVector();
            if (!LoaderManager::getLandLoader()->DoingRegister($pl)) return;
            if (LoaderManager::getLandLoader()->getAreaByPosition(new Position($bl->x, $bl->y, $bl->z, $pl->level)) == \null) return;
            if (!LoaderManager::getLandLoader()->isFirstVecRegister($pl)) {
                LoaderManager::getLandLoader()->FirstVecRegister($pl, new Vector2($bl->x, $bl->z));
                $pl->sendMessage("Registered First Vector2");
            } elseif (!LoaderManager::getLandLoader()->isSecondVecRegister($pl)) {
                LoaderManager::getLandLoader()->SecondVecRegister($pl, new Vector2($bl->x, $bl->z));
                $pl->sendMessage("Registered all");
                $v1 = LoaderManager::getLandLoader()->getRegisters($pl)[2];
                $v2 = LoaderManager::getLandLoader()->getRegisters($pl)[3];
                if ($v1->x > $v2->x) {
                    $mnx = $v2->x;
                    $mxx = $v1->x;
                } else {
                    $mnx = $v1->x;
                    $mxx = $v2->x;
                }
                if ($v1->y > $v2->y) {
                    $mny = $v2->y;
                    $mxy = $v1->y;
                } else {
                    $mny = $v1->y;
                    $mxy = $v2->y;
                }
                $mnv = new Vector2($mnx, $mny);
                $mxv = new Vector2($mxx, $mxy);
                LoaderManager::getLandLoader()->addArea(new LandArea(LoaderManager::getLandLoader()->registeringNum($pl), LoaderManager::getLandLoader()->registeringLevel($pl), $mnv, $mxv));
            }
        }
    }