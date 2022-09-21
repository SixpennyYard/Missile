<?php

namespace SixpennyYard\Missile\Event;


use pocketmine\event\Listener;
use pocketmine\world\Explosion;
use pocketmine\world\Position;

class EventListener implements Listener
{

    public static function missileExplosion($x, $y, $z, $world){
        $explosion = new Explosion(new Position($x, $y, $z, $world), 3.4);
        $explosion->explodeA();
        $explosion->explodeB();
    }

}