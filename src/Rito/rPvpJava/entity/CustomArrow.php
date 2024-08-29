<?php

namespace Rito\rPvpJava\entity;

use pocketmine\entity\animation\HurtAnimation;
use pocketmine\entity\projectile\Arrow;
use pocketmine\player\Player;

class CustomArrow extends Arrow {

    public function onUpdate(int $currentTick): bool {
        $hasUpdate = parent::onUpdate($currentTick);

        if(!$this->isClosed() && !$this->isFlaggedForDespawn()) {
            $owner = $this->getOwningEntity();
            if($owner instanceof Player) {
                foreach($this->getWorld()->getEntities() as $entity) {
                    if($entity instanceof Player && $entity->getId() === $owner->getId() && $entity->getPosition()->distanceSquared($this->getPosition()) < 1) {

                        $entity->setMotion($entity->getDirectionVector()->multiply(1));
                        if ($entity->getHealth() > 1.0) {
                            $entity->setHealth($entity->getHealth() - 1.0);
                        }
                        $entity->broadcastAnimation(new HurtAnimation($entity));

                        $this->flagForDespawn();
                        break;
                    }
                }
            }
        }

        return $hasUpdate;
    }
}