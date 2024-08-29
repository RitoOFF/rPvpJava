<?php

namespace Rito\rPvpJava;


use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use Rito\rPvpJava\entity\CustomArrow;
use Rito\rPvpJava\listener\AttackListener;
use Rito\rPvpJava\listener\EventListener;

trait Loader
{
    public function init(): void
    {
        $main = Main::getInstance();
        $main->getResource("config.yml");
        $main->saveDefaultConfig();

        $listeners = [
            new AttackListener(),
            new EventListener()
        ];

        foreach ($listeners as $listener) {
            $main->getServer()->getPluginManager()->registerEvents($listener, $this);
        }

        EntityFactory::getInstance()->register(CustomArrow::class, function(World $world, CompoundTag $nbt): CustomArrow {
            return new CustomArrow(EntityDataHelper::parseLocation($nbt, $world), null,false, $nbt);
        }, ["custom_arrow", "minecraft:custom_arrow"]);

    }
}