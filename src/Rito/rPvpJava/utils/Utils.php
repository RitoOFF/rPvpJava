<?php

namespace Rito\rPvpJava\utils;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\world\World;

class Utils
{
    private const PARTICLE_CRITICAL = "dave:critical_hit_custom";
    private const PARTICLE_SWEEP = "dave:sweep";
    private const DEFAULT_DIMENSION = DimensionIds::OVERWORLD;
    private const DEFAULT_ENTITY_ID = -1;

    private static function spawnParticle(World $world, Vector3 $position, string $particleType, int $count = 1): void
    {
        $packet = SpawnParticleEffectPacket::create(
            self::DEFAULT_DIMENSION,
            self::DEFAULT_ENTITY_ID,
            $position,
            $particleType,
            null
        );

        for ($i = 0; $i < $count; $i++) {
            $world->broadcastPacketToViewers($position, $packet);
        }
    }

    public static function spawnParticleCritical(World $world, Location $location): void
    {
        $particlePos = $location->add(0, 0.625, 0);
        self::spawnParticle($world, $particlePos, self::PARTICLE_CRITICAL, 3);
    }

    public static function spawnParticleSweep(World $world, Vector3 $vector3): void
    {
        self::spawnParticle($world, $vector3, self::PARTICLE_SWEEP);
    }
}
