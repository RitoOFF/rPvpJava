<?php

namespace Rito\rPvpJava\task;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\Server;
use Rito\rPvpJava\listener\AttackListener;

class CooldownAttackTask extends Task
{
    public function onRun(): void
    {
        foreach (AttackListener::$cooldownAttack as $name => $time) {
            $player = Server::getInstance()->getPlayerByPrefix($name);
            if ($player instanceof Player) {
                if ($player->isOnline()) {
                    if (AttackListener::$cooldownAttack[$name] < time()) {
                        unset(AttackListener::$cooldownAttack[$name]);
                        $this->getHandler()->cancel();
                    }
                }else{
                    unset(AttackListener::$cooldownAttack[$name]);
                    $this->getHandler()->cancel();
                }
            } else {
                unset(AttackListener::$cooldownAttack[$name]);
                $this->getHandler()->cancel();
            }
        }
    }
}