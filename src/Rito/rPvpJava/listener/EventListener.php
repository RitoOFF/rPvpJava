<?php

namespace Rito\rPvpJava\listener;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use Rito\rPvpJava\entity\CustomArrow;

class EventListener implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $swap = [
            VanillaItems::ARROW()->getTypeId(),
            VanillaItems::TOTEM()->getTypeId()
        ];
        if (in_array($item->getTypeId(),$swap)){
            $offHand = $player->getOffHandInventory()->getItem(0);
            $invHand = $player->getInventory()->getItemInHand();
            $player->getOffHandInventory()->setItem(0, $invHand);
            $player->getInventory()->setItemInHand($offHand);
        }
    }
    public function onShootBow(EntityShootBowEvent $event): void {
        $shooter = $event->getEntity();
        $bow = $event->getBow();
        $power = $event->getForce();

        var_dump($bow->getEnchantments());
        if (!$bow->hasEnchantment(VanillaEnchantments::PUNCH())) return;
        $projectile = $event->getProjectile();
        if($projectile instanceof Arrow) {
                if ($power <= 0.8 ) {
                    if (!$shooter->getMovementSpeed() >= 0.0){
                        $event->cancel();
                        $baseForce = min(((5 ** 2) + 5 * 2) / 3, 1);

                        $direction = $shooter->getDirectionVector();
                        $positionDerriere = new Location(
                            $shooter->getLocation()->getX() - $direction->x * 2,
                            $shooter->getLocation()->getY() - $direction->y * 2,
                            $shooter->getLocation()->getZ() - $direction->z * 2,
                            $shooter->getLocation()->getWorld(),
                            $shooter->getLocation()->getYaw(),
                            $shooter->getLocation()->getPitch()
                        );
                        $customArrow = new CustomArrow($positionDerriere, $shooter, $baseForce >= 1);
                        $customArrow->setMotion($shooter->getDirectionVector());
                        $customArrow->spawnToAll();
                    }
                }
            }
        }

}