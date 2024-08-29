<?php

namespace Rito\rPvpJava\player;

use pocketmine\entity\Attribute;
use pocketmine\player\Player;
class CustomPlayer extends Player {

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $xKB = (float)"2.70";
        $yKB = (float)"0.05";

        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;

            $motion = clone $this->motion;

            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $xKB;
            $motion->y += $yKB;
            $motion->z += $z * $f * $xKB;

            if ($motion->y > $yKB) {
                $motion->y = $yKB;
            }

            $this->setMotion($motion);
        }
    }
}