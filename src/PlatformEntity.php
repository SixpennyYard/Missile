<?php

namespace SixpennyYard\Missile;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SixpennyYard\Missile\Event\EventListener;

class PlatformEntity extends Human{

    public static function getNetworkTypeId(): string
    {
        return "platform:mob";
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        $item = $player->getInventory()->getItemInHand();
        if ($player->isSneaking()){
            $this->flagForDespawn();
            $item = VanillaItems::NETHER_STAR();
            $item->setCustomName("§rPlatform");
            $item->setLore(["Platform"]);
            $player->getInventory()->addItem($item);

        }elseif($item->getCustomName() == "§rMissile T1" && $item->getLore() == ["Missile"]){
            $this->flagForDespawn();
            Main::getInstance()->createPlatformAndMissile($player, new Location($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ(), $this->getWorld(), 0, 0));
        }else{
            EventListener::platformForm($player, $this, new Location($this->getPosition()->getX(), $this->getPosition()->getY() + 1, $this->getPosition()->getZ(), $this->getWorld(), 0, 180), false);
        }

        return true;
    }


}