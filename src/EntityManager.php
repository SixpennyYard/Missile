<?php

namespace SixpennyYard\Missile;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\entity\Location;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{CompoundTag, StringTag, ByteArrayTag, DoubleTag, FloatTag, ListTag, ShortTag};
use SixpennyYard\Missile\Main;

class EntityManager {

    public static function getSkin($Texturefile, $geometryFile, $geometryIdentifier, $newName = "NewSkin"){
        $texturePath = Main::getInstance()->getDataFolder() . $Texturefile;
        if(!file_exists($texturePath)) return null;
        $img = @imagecreatefrompng($texturePath);
        $size = getimagesize($texturePath);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);

        $modelPath = Main::getInstance()->getDataFolder() . $geometryFile;
        $newskin = new Skin($newName, $skinbytes, "", $geometryIdentifier, file_get_contents($modelPath));
        return $newskin;
    }

    public static function getNBT(Player $player, Location $location){
        $nbt = self::createBaseNBT($location->asVector3(), null, $location->getYaw(), $location->getPitch());
        $nbt->setTag("Skin", CompoundTag::create()
            ->setString("Name", $player->getSkin()->getSkinId())
            ->setByteArray("Data", $player->getSkin()->getSkinData())
            ->setByteArray("CapeData", $player->getSkin()->getCapeData())
            ->setString("GeometryName", $player->getSkin()->getGeometryName())
            ->setByteArray("GeometryData", $player->getSkin()->getGeometryData())
        );
        return $nbt;
    }

    public static function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag {
        return CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($pos->x),
                new DoubleTag($pos->y),
                new DoubleTag($pos->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0.0),
                new DoubleTag($motion !== null ? $motion->y : 0.0),
                new DoubleTag($motion !== null ? $motion->z : 0.0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }
}
