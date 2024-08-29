<?php

namespace Rito\rPvpJava\listener;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\item\Axe;
use pocketmine\item\Sword;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use Rito\rPvpJava\Main;
use Rito\rPvpJava\player\CustomPlayer;
use Rito\rPvpJava\sound\SweepSound;
use Rito\rPvpJava\task\CooldownAttackTask;
use Rito\rPvpJava\utils\Utils;

class AttackListener implements Listener
{
    public static array $cooldownAttack = [];

    private const DAMAGE_PERCENTAGES = [1, 0.92, 0.8, 0.7, 0.6, 0.54, 0.43, 0.36, 0.33, 0.2];


    public function onEntityDamage(EntityDamageByEntityEvent $event) : void
    {
        $entity = $event->getEntity();
        if ($entity instanceof CustomPlayer) {
            $event->setAttackCooldown(0.4);
        }
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();

        if ($event->getModifier(EntityDamageEvent::MODIFIER_CRITICAL) > 0) {
            $event->cancel();
        }

        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if (!$damager instanceof Player) return;

            $itemInHand = $damager->getInventory()->getItemInHand();
            $typeId = $itemInHand->getTypeId();
            $cooldownDurations = [
                VanillaItems::DIAMOND_SWORD()->getTypeId() => 0.6,
                VanillaItems::IRON_SWORD()->getTypeId() => 0.6,
                VanillaItems::WOODEN_SWORD()->getTypeId() => 0.6,
                VanillaItems::GOLDEN_SWORD()->getTypeId() => 0.6,
                VanillaItems::NETHERITE_SWORD()->getTypeId() => 0.6,
                VanillaItems::STONE_SWORD()->getTypeId() => 0.6,
                VanillaItems::WOODEN_AXE()->getTypeId() => 1.25,
                VanillaItems::STONE_AXE()->getTypeId() => 1.25,
                VanillaItems::IRON_AXE()->getTypeId() => 1.1,
            ];
            $cooldownDuration = $cooldownDurations[$typeId] ?? 1;

            $damagerName = $damager->getName();

            if (isset(self::$cooldownAttack[$damagerName])) {
                $timeLeft = self::$cooldownAttack[$damagerName] - time();

                if (!$timeLeft > 0) {
                    unset(self::$cooldownAttack[$damagerName]);
                    return;
                }

                $index = min(intval(($timeLeft / $cooldownDuration) * count(self::DAMAGE_PERCENTAGES)), count(self::DAMAGE_PERCENTAGES) - 1);
                if (!isset(self::DAMAGE_PERCENTAGES[$index])){
                    return;
                }
                $damageMultiplier = self::DAMAGE_PERCENTAGES[$index];
                $event->setBaseDamage(($event->getBaseDamage() * $damageMultiplier) / 4);

            } else {
                $event->setBaseDamage($event->getBaseDamage() - 2);
                self::$cooldownAttack[$damagerName] = time() + $cooldownDuration;
            }

            if (!$damager->isOnGround() && !$damager->isUnderwater()) {
                $event->setModifier($event->getBaseDamage(), EntityDamageEvent::MODIFIER_CRITICAL);
                Utils::spawnParticleCritical($entity->getWorld(), $entity->getLocation());
            }

            if ($itemInHand instanceof Sword && $damager->isOnGround() && !$damager->isUnderwater() && !$damager->isSprinting()) {
                $this->handleSweepAttack($damager, $entity);
            }
            if ($damager->isSprinting() && !$damager->isOnGround() && !$damager->isUnderwater()){
                $this->handleSprintAttack($damager, $entity);
            }

            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownAttackTask(), 20);
            $this->sendUnicodeActionBar($damager);
        }
    }

    private function handleSprintAttack(Player $damager, Entity $entity): void{
        if ($damager->getInventory()->getItemInHand() instanceof Axe or $damager->getInventory()->getItemInHand() instanceof Sword) {
            $directionVector = $entity->getPosition()->subtract($damager->getPosition()->x, $damager->getPosition()->y, $damager->getPosition()->z)->normalize();
            $entity->setMotion($entity->getMotion()->add($directionVector->multiply(1)->x, $directionVector->multiply(1)->y,$directionVector->multiply(1)->z));
        }
    }
    private function handleSweepAttack(Player $damager, Entity $entity): void
    {
        $position = $damager->getPosition();
        $direction = $damager->getLocation()->getYaw() * (M_PI / 180);
        $offsetX = -sin($direction) * 2;
        $offsetZ = cos($direction) * 2;
        $sweepPosition = new Vector3($position->getX() + $offsetX, $position->getY() + 0.2, $position->getZ() + $offsetZ);

        Utils::spawnParticleSweep($damager->getWorld(), $sweepPosition);
        $damager->broadcastSound(new SweepSound());

        $nearbyEntities = $damager->getWorld()->getNearbyEntities($entity->getBoundingBox()->expandedCopy(1.5, 1.5, 1.5));
        foreach ($nearbyEntities as $nearbyEntity) {
            if ($nearbyEntity->getId() === $damager->getId() || $nearbyEntity->getId() === $entity->getId()) {
                continue;
            }

            $damage = 3 * (mt_rand(50, 75) / 100);
            $nearbyEntity->attack(new EntityDamageEvent($nearbyEntity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage));

            $directionVector = $nearbyEntity->getPosition()->subtract($position->x, $position->y, $position->z)->normalize();
            $nearbyEntity->setMotion($nearbyEntity->getMotion()->add($directionVector->multiply(1)->x, $directionVector->multiply(1)->y,$directionVector->multiply(1)->z));
        }
    }

    private function sendUnicodeActionBar(Player $player): void
    {
        $unicodeMessage = "";
       // $player->sendActionBarMessage($unicodeMessage);

        foreach (str_split($unicodeMessage) as $unicode) {
            $player->sendActionBarMessage($unicode);
        }
    }
}
