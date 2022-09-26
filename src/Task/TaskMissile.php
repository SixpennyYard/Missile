<?php

namespace SixpennyYard\Missile\Task;

use pocketmine\scheduler\Task;
use SixpennyYard\Missile\EntityMissile;
use SixpennyYard\Missile\Main;
use SixpennyYard\Missile\PlatformEntity;
use SixpennyYard\Missile\PlatformEntityEntity;

class TaskMissile extends Task
{

    public function __construct(Main $pl)
    {
        $this->pl = $pl;
    }

    public function onRun(): void
    {
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $w) {
            foreach ($w->getEntities() as $ent) {
                if ($ent instanceof EntityMissile) {
                    $ent->setScale(1);
                }elseif ($ent instanceof PlatformEntity){
                    $ent->setScale(1);
                }
            }
        }
    }
}
