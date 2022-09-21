<?php


namespace SixpennyYard\Missile;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use SixpennyYard\Machine\EntityManager;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use TaskMissile;

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
        }, ["EntityMachine"]);
        $this->getScheduler()->scheduleRepeatingTask(new TaskMissile($this), 20);
    }


    public function onCommand(CommandSender $p, Command $cmd, String $label, array $args) : bool{
        if($cmd->getName() === 'missile'){
            if ($p instanceof Player) {
                $item = VanillaItems::NETHER_STAR();
                $item->setCustomName("§rMissile T1");
                $item->setLore(["Missile"]);
                $p->getInventory()->addItem($item);
            }
            return true;
        }
        return true;
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $pos = $event->getPlayer()->getLocation();
        $item = $player->getInventory()->getItemInHand();
        if($item->getCustomName() == "§rMissile T1" && $item->getLore() == ["Missile"]){
            $skin = EntityManager::getSkin("missile.png", "missile.json", "geometry.missile", "Missile");
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
            $missile->sendSkin($this->getServer()->getOnlinePlayers());
            $missile->spawnToAll();
            $item->setCount($item->getCount() - 1);
        }
    }
    public function hitMissile(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $player = $event->getDamager();

        if ($entity instanceof EntityMissile){
            if ($player instanceof PLayer){
                $event->cancel();
                if ($player->isSneaking()){
                    $entity->flagForDespawn();
                }
            }else{
                $event->cancel();
            }
        }
    }
}