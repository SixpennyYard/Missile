<?php

namespace SixpennyYard\Missile\Event;


use EasyUI\element\Input;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\Explosion;
use pocketmine\world\Position;
use SixpennyYard\Missile\EntityMissile;
use SixpennyYard\Missile\FormEntity;
use SixpennyYard\Missile\Main;
use SixpennyYard\Missile\PlatformEntity;

class EventListener implements Listener
{

    public static function missileExplosion($x, $y, $z, $world){
        $explosion = new Explosion(new Position($x, $y, $z, $world), 3.4);
        $explosion->explodeA();
        $explosion->explodeB();
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $item = VanillaItems::NETHER_STAR();
        $item->setCustomName("§rMissile T1");
        $item->setLore(["Missile"]);
        $player->getInventory()->addItem($item);
        $plat = VanillaItems::NETHER_STAR();
        $plat->setCustomName("§rPlatform");
        $plat->setLore(["Platform"]);
        $player->getInventory()->addItem($plat);
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $x = $event->getBlock()->getPosition()->getX();
        $y = $event->getBlock()->getPosition()->getY() + 1;
        $z = $event->getBlock()->getPosition()->getZ();
        $world = $event->getBlock()->getPosition()->getWorld();
        $pos = new Location($x, $y, $z, $world, 0, 0);
        $item = $player->getInventory()->getItemInHand();

        if ($item->getCustomName() == "§rPlatform" && $item->getLore() == ["Platform"]){
            Main::getInstance()->createPlatform($player, $pos);
        }
    }

    public function onHit(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $pos = $event->getEntity()->getLocation();
        $player = $event->getDamager();
        if ($player instanceof Player) {
            if ($entity instanceof PlatformEntity) {
                $event->cancel();
            }elseif ($entity instanceof FormEntity){
                $event->cancel();
            }
        }else{
            $event->cancel();
        }
    }

    public static function platformForm(Player $player, Entity $entity, Location $pos, bool $value)
    {
        $xplayer = $player->getPosition()->getX();
        $zplayer = $player->getPosition()->getZ();

        $form = new CustomForm("Platform");
        $form->addElement("xcoord", new Input("Coordonnée en X", $xplayer));
        $form->addElement("zcoord", new Input("Coordonnée en Z", $zplayer));

        $form->setSubmitListener(function (Player $player, FormResponse $response) use ($xplayer, $zplayer, $value, $entity, $pos){
            $x = $response->getInputSubmittedText("xcoord");
            $y = $response->getInputSubmittedText("zcoord");
            if ($x !== $xplayer or $y !== $zplayer){
                if ($value !== false) {
                    $position = new Location($x, 200, $y, $player->getWorld(), 0, 180);
                    Main::getInstance()->createMissile($player, $position);
                    $entity->flagForDespawn();
                    Main::getInstance()->createPlatform($player, $pos);
                }
            }
        });
        $player->sendForm($form);
    }
}