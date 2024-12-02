<?php

namespace unearthly\randomtp;

use pocketmine\plugin\PluginBase;
use unearthly\randomtp\command\RandomTeleportCommand;

class Main extends PluginBase
{
    private static self $instance;

    function onEnable() : void
    {
        self::$instance = $this;
        $this->getServer()->getCommandMap()->register("", new RandomTeleportCommand());
    }

    /**
     * @return $this
     */
    public static function getInstance() : self
    {
        return self::$instance;
    }
}
