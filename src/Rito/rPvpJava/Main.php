<?php


namespace Rito\rPvpJava;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase{

    use SingletonTrait;
    use Loader;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->init();
        $this->getLogger()->notice("ENABLE -> Plugin rPvpJava BY RITO | discord: rito.off");

    }
    public function onDisable(): void
    {
        $this->getLogger()->notice("DISABLE ->  Plugin rPvpJava BY RITO | discord: rito.off");
    }
}