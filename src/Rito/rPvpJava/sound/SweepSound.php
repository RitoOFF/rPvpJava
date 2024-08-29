<?php

namespace Rito\rPvpJava\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\world\sound\Sound;

class SweepSound implements Sound
{
    public function encode(Vector3 $pos): array
    {
        return [LevelSoundEventPacket::nonActorSound((int)"sweep",$pos,false)];
    }
}