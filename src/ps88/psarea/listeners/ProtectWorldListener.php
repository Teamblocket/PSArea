<?php
    namespace ps88\psarea\listeners;

    use pocketmine\event\block\BlockBreakEvent;
    use pocketmine\event\block\BlockPlaceEvent;
    use pocketmine\event\Listener;
    use ps88\psarea\loaders\LoaderManager;
    use ps88\psarea\worldmanager\ProtectWorld;

    class ProtectWorldListener implements Listener {

        public function BlockBreak(BlockBreakEvent $ev) {
            $pl = $ev->getPlayer();
            if ($pl->isOp()) return;
            if ((($a = LoaderManager::$islandloader->getAreaByVector($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'island') or (($a = LoaderManager::$skylandloader->getAreaByVector3($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'skyland') or (($a = LoaderManager::$fieldloader->getAreaByVector3($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'field')) {
                if ($a->owner == \null) {
                    if ($a->getShare($pl->getName()) == \null) {
                        $ev->setCancelled();
                        $pl->sendMessage("You don't have permission to do it");
                    }
                } elseif ($a->owner->getName() !== $pl->getName()) {
                    if ($a->getShare($pl->getName()) == \null) {
                        $ev->setCancelled();
                        $pl->sendMessage("You don't have permission to do it");
                    }
                } elseif ($a->owner->getName() == $pl->getName()) {
                    return;
                }
            }
            if (!ProtectWorld::getInstance()->isLevelProtected($pl->getLevel())) {
                if (($a = LoaderManager::$landloader->getAreaByPosition($pl->asPosition())) !== null) {
                    if ($a->owner == \null) {
                        $ev->setCancelled();
                        return;
                    }
                    if ($a->owner->getName() == $pl->getName() or $a->getShare($pl->getName()) !== \null) {
                        return;
                    }
                }
                $ev->setCancelled();
                return;
            } else {
                $ev->setCancelled();
            }
            return;
        }

        public function BlockPlace(BlockPlaceEvent $ev) {
            $pl = $ev->getPlayer();
            if ($pl->isOp()) return;
            if ((($a = LoaderManager::$islandloader->getAreaByVector3($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'island') or (($a = LoaderManager::$skylandloader->getAreaByVector3($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'skyland') or (($a = LoaderManager::$fieldloader->getAreaByVector3($ev->getBlock()->asVector3())) !== null and $pl->level->getName() == 'field')) {
                if ($a->owner == \null) {
                    if ($a->getShare($pl->getName()) == \null) {
                        $ev->setCancelled();
                        $pl->sendMessage("You don't have permission to do it");
                    }
                } elseif ($a->owner->getName() !== $pl->getName()) {
                    if ($a->getShare($pl->getName()) == \null) {
                        $ev->setCancelled();
                        $pl->sendMessage("You don't have permission to do it");
                    }
                } elseif ($a->owner->getName() == $pl->getName()) {
                    return;
                }
            }
            if (!ProtectWorld::getInstance()->isLevelProtected($pl->getLevel())) {
                if (($a = LoaderManager::$landloader->getAreaByPosition($pl->asPosition())) !== null) {
                    if ($a->owner == \null) {
                        $ev->setCancelled();
                        return;
                    }
                    if ($a->owner->getName() == $pl->getName() or $a->getShare($pl->getName()) !== \null) {
                        return;
                    }
                }
                $ev->setCancelled();
                return;
            } else {
                $ev->setCancelled();
            }
            return;
        }
    }