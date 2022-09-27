<?php


namespace SixpennyYard\Missile;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use SixpennyYard\Missile\Task\TaskMissile;

class Main extends PluginBase {

    private static Main $instance;

    public static function getInstance(): Main {
        return self::$instance;
    }


    public function onEnable(): void {
        self::$instance = $this;

        $files = ["machine.png", "machine.json"];
        foreach($files as $file) {
            $this->saveResource($file);
        }
        EntityFactory::getInstance()->register(EntityMissile::class, function ($world, $nbt) : EntityMissile {
            return new EntityMissile(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["EntityMissile"]);
        EntityFactory::getInstance()->register(PlatformEntity::class, function ($world, $nbt) : PlatformEntity {
            return new PlatformEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["PlatformEntity"]);
        EntityFactory::getInstance()->register(FormEntity::class, function ($world, $nbt) : FormEntity {
            return new FormEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["FormEntity"]);

        $this->getScheduler()->scheduleRepeatingTask(new TaskMissile($this), 20);
        $this->getServer()->getPluginManager()->registerEvents(new Event\EventListener(), $this);
    }


    /**
     */
    public function createMissile(Player $player, Location $pos){
        $skin = EntityManager::getSkin("machine.png", "machine.json", "geometry.machine", "Missile");
        $nbt = EntityManager::getNBT($player, $pos);
        $missile = new EntityMissile($pos, $skin, $nbt);
        $missile->setSkin($skin);
        $missile->setScale(1);
        $missile->setImmobile();
        $missile->setNameTag("Missile");
        $missile->setHasGravity(true);
        $missile->setForceMovementUpdate(false);
        $missile->setNameTagVisible(false);
        $missile->setNameTagAlwaysVisible(false);
        $missile->sendSkin(Main::getInstance()->getServer()->getOnlinePlayers());
        $missile->spawnToAll();
    }

    public function createPlatform(Player $player, $pos){
        $skin = EntityManager::getSkin("machine.png", "machine.json", "geometry.machine", "Platform");
        $nbt = EntityManager::getNBT($player, $pos);
        $platform = new PlatformEntity($pos, $skin, $nbt);
        $platform->setSkin($skin);
        $platform->setScale(1);
        $platform->setImmobile();
        $platform->setNameTag("Platform");
        $platform->setHasGravity(true);
        $platform->setForceMovementUpdate(false);
        $platform->setNameTagVisible(false);
        $platform->setNameTagAlwaysVisible(false);
        $platform->sendSkin(Main::getInstance()->getServer()->getOnlinePlayers());
        $platform->spawnToAll();
    }

    public function createPlatformAndMissile(Player $player, $pos){
        $skin = EntityManager::getSkin("machine.png", "machine.json", "geometry.machine", "PlatformAndMissile");
        $nbt = EntityManager::getNBT($player, $pos);
        $platform = new FormEntity($pos, $skin, $nbt);
        $platform->setSkin($skin);
        $platform->setScale(1);
        $platform->setImmobile();
        $platform->setNameTag("Platform avec Missile");
        $platform->setHasGravity();
        $platform->setForceMovementUpdate(false);
        $platform->setNameTagVisible(false);
        $platform->setNameTagAlwaysVisible(false);
        $platform->sendSkin(Main::getInstance()->getServer()->getOnlinePlayers());
        $platform->spawnToAll();
    }
}
