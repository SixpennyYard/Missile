<?php


namespace SixpennyYard\Missile;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\world\Explosion;
use pocketmine\world\Position;
use SixpennyYard\Machine\EntityManager;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

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
            $arg = array_shift($args);
            switch($arg){
                case "remover":
                    $remover = VanillaItems::STICK();
                    $remover->setCustomName("§r§4Remover");
                    $remover->setLore(["remover"]);
                    $p->getInventory()->addItem($remover);
                    break;
                default:
                    $item = VanillaItems::NETHER_STAR();
                    $item->setCustomName("§rMissile T1");
                    $item->setLore(["Missile"]);
                    $p->getInventory()->addItem($item);
                    break;
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
            $missile->setImmobile(true);
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
                if ($player->isSneaking()){
                    $entity->flagForDespawn();
                }
            }
        }
    }
    public function onDeath(EntityDeathEvent $event){
        $entity = $event->getEntity();

        if ($entity instanceof EntityMissile){
            $explosion = new Explosion(new Position($entity->getPosition()->getX, $entity->getPosition()->getY,$entity->getPosition()->getZ, $entity->getWorld()), 3.4, $entity);
            $explosion->explodeA();
            $explosion->explodeB();
        }
    }
    public function onExplodeEntity(EntityExplodeEvent $event){
        $entity = $event->getEntity();
        $block = $entity->getWorld()->getBlock($entity->getPosition());
        $list = [];
        if ($entity instanceof EntityMissile){
            if (!$event->isCancelled()){
                for($i = 0; $i <= (3.3*2); $i++) {
                    $list[] = $block->getSide($i);
                }
            }
        }
    }
}

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
                    $ent->setNameTagAlwaysVisible(false);
                    $ent->setNameTagVisible(true);
                    $ent->setScale(1);
                }
            }
        }
    }
}