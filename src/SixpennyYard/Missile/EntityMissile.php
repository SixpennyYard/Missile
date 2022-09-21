<?php

namespace SixpennyYard\Missile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use SixpennyYard\Missile\Event\EventListener;

class EntityMissile extends Human{

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1, 1.2, 0.8);
    }

    public static function getNetworkTypeId(): string
    {
        return "missile:mob";
    }

    public function onCollideWithPlayer(Player $player): void
    {
        $x = $this->getPosition()->getX();
        $y = $this->getPosition()->getY();
        $z = $this->getPosition()->getZ();
        $world = $this->getPosition()->getWorld();

        EventListener::missileExplosion($x, $y, $z, $world);

        parent::onCollideWithPlayer($player);
    }

    public function onHitGround(): ?float
    {
        $x = $this->getPosition()->getX();
        $y = $this->getPosition()->getY();
        $z = $this->getPosition()->getZ();
        $world = $this->getPosition()->getWorld();

        EventListener::missileExplosion($x, $y, $z, $world);

        return parent::onHitGround();
    }

    protected function onDeath(): void
    {
        $x = $this->getPosition()->getX();
        $y = $this->getPosition()->getY();
        $z = $this->getPosition()->getZ();
        $world = $this->getPosition()->getWorld();

        EventListener::missileExplosion($x, $y, $z, $world);

        parent::onDeath();
    }

}