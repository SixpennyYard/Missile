<?php

namespace SixpennyYard\Missile;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use SixpennyYard\Missile\Event\EventListener;

class FormEntity extends Human{

    public static function getNetworkTypeId(): string
    {
        return "formentity:mob";
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if ($player->isSneaking()){
            $this->flagForDespawn();
            $missile = VanillaItems::NETHER_STAR();
            $missile->setCustomName("§rMissile T1");
            $missile->setLore(["Missile"]);
            $player->getInventory()->addItem($missile);
            $item = VanillaItems::NETHER_STAR();
            $item->setCustomName("§rPlatform");
            $item->setLore(["Platform"]);
            $player->getInventory()->addItem($item);
        }else {
            EventListener::platformForm($player, $this, new Location($this->getPosition()->getX(), $this->getPosition()->getY() + 1, $this->getPosition()->getZ(), $this->getWorld(), 0, 180), true);
        }
        return true;
    }

}