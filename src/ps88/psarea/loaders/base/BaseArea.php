<?php
    namespace ps88\psarea\loaders\base;

    use pocketmine\level\Position;
    use pocketmine\math\Vector2;
    use pocketmine\IPlayer;
    use pocketmine\Player;
    use pocketmine\Server;
    use pocketmine\utils\TextFormat;
    use ps88\psarea\events\area\PSAreaAddShareEvent;
    use ps88\psarea\events\area\PSAreaEnterEvent;
    use ps88\psarea\events\area\PSAreaWarpEvent;
    use ps88\psarea\managers\PlayerManager;
    use ps88\psarea\translator\Translator;

    abstract class BaseArea {
        public const Island = 0;
        public const Field = 1;
        public const Skyland = 2;
        public const Land = 3;

        /** @var int */
        public const LandType = -1;

        /** @var Vector2 */
        public $minvec;

        /** @var Vector2 */
        public $maxvec;

        /** @var IPlayer|null */
        public $owner = \null;

        /** @var IPlayer[] */
        public $shares = [];

        /** @var bool */
        public $access = \true;

        /** @var int */
        private $landnum;

        /** @var string */
        private $welcomemessage = "";

        /** @var string[] */
        private $PlayingPlayers = [

        ];

        /**
         * BaseArea constructor.
         * @param int $landnum
         * @param Vector2 $minvec
         * @param Vector2 $maxvec
         * @param IPlayer|null $owner
         * @param IPlayer[] $shares
         */
        public function __construct(int $landnum, Vector2 $minvec, Vector2 $maxvec, ?IPlayer $owner = \null, array $shares = []) {
            $this->landnum = $landnum;
            $this->minvec = $minvec;
            $this->maxvec = $maxvec;
            $this->owner = $owner;
            $this->shares = $shares;
        }

        /**
         * @return Vector2
         */
        public function getMinVector(): Vector2 {
            return $this->minvec;
        }

        /**
         * @param Vector2 $minvec
         */
        public function setMinVector(Vector2 $minvec): void {
            $this->minvec = $minvec;
        }

        /**
         * @return Vector2
         */
        public function getMaxVector(): Vector2 {
            return $this->maxvec;
        }

        /**
         * @param Vector2 $maxvec
         */
        public function setMaxVector(Vector2 $maxvec): void {
            $this->maxvec = $maxvec;
        }

        /**
         * @return null|IPlayer
         */
        public function getOwner(): ?IPlayer {
            return $this->owner;
        }

        /**
         * @param null|IPlayer $owner
         */
        public function setOwner(?IPlayer $owner): void {
            $this->owner = $owner;
        }

        /**
         * @return IPlayer[]
         */
        public function getShares(): array {
            return $this->shares;
        }

        public function getShare(string $name): ?IPlayer {
            foreach ($this->getShares() as $IPlayer) {
                if ($IPlayer->getName() == $name) return $IPlayer;
            }
            return \null;
        }

        public function addShare(IPlayer $pl): void {
            if ($this->getShare($pl->getName()) !== \null) return;
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaAddShareEvent($this, $pl));
            if ($ev->isCancelled()) return;
            array_push($this->shares, $pl);
        }

        public function delShare(IPlayer $pl): void {
            if ($this->getShare($pl->getName()) == \null) return;
            for ($i = 0; $i >= count($this->getShares()); $i++) {
                $share = $this->shares[$i];
                if ($share->getName() == $pl->getName()) {
                    Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaAddShareEvent($this, $pl));
                    if ($ev->isCancelled()) return;
                    unset($this->shares[$i]);
                    return;
                }
            }
        }

        /**
         * @return int
         */
        public function getLandnum(): int {
            return $this->landnum;
        }

        public function Warp(Player $pl): bool {
            $v = $this->getMinVector();
            $v2 = $this->getMaxVector();
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaWarpEvent($this, $pl));
            if ($ev->isCancelled()) return \false;
            $pl->teleport(new Position(($v->x + $v2->x) / 2, 14, ($v->y + $v2->y) / 2, Server::getInstance()->getLevelByName('island')));
            return \true;
        }

        public function PlayerAccess(Player $player): void {
            Server::getInstance()->getPluginManager()->callEvent($ev = new PSAreaEnterEvent($this, $player));
            if (PlayerManager::$PlayingPlayers[$player->getName()][0] == self::LandType and PlayerManager::$PlayingPlayers[$player->getName()][1] == $this->getLandnum()) return;
            if ($ev->isCancelled() or (!$this->access and ($this->getOwner()->getName() !== $player->getName() or $this->getShare($player->getName()) == \null))) { // Can't Access
                $player->sendPopup(TextFormat::RED . Translator::get("cant-access", \false));
                $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
                return;
            } else { // Access!!
                $type = $this->TypeAsString();
                $msg = ($this->getOwner() == \null) ? Translator::get("welcome-to-type", \false, ["@type", $type], ["@landnum", $this->getLandnum()]) . "\n" . Translator::get("no-owner", \false, ["@type", $type], ["@landnum", $this->getLandnum()]) : $this->getWelcomeMessage();
                $player->sendPopup($msg);
            }
        }

        public function PlayerOuit(Player $player) {

        }

        protected function TypeAsString(): string {
            return "Nothing";
        }

        /**
         * @return string
         */
        public function getWelcomeMessage(): string {
            return $this->welcomemessage;
        }

        /**
         * @param string $welcomemessage
         */
        public function setWelcomeMessage(string $welcomemessage): void {
            $this->welcomemessage = $welcomemessage;
        }
    }