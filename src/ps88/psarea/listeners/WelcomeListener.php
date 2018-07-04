<?php
    namespace ps88\psarea\listeners;

    use pocketmine\event\entity\EntityTeleportEvent;
    use pocketmine\event\Listener;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\level\Position;
    use pocketmine\Player;
    use ps88\psarea\loaders\LoaderManager;
    use ps88\psarea\manager\PlayerManager;

    class WelcomeListener implements Listener {

        public function move(PlayerMoveEvent $e): void {
            $this->a($e->getPlayer(), $e->getTo());
        }

        public function tel(EntityTeleportEvent $e): void {
            $p = $e->getEntity();
            if (!$p instanceof Player) return;
            $this->a($p, $e->getTo());
        }

        public function a(Player $p, Position $to): void {
            PlayerManager::PlayerEnter(LoaderManager::getArea($to), $p);
        }
    }