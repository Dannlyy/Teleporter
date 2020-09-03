<?php


namespace Teleporter;

use Teleporter\listener\Compass;
use pocketmine\plugin\PluginBase;

class Core extends PluginBase
{

    private static $instance;
    const PREFIX = "§3[§7Teleporter§3] §f";

    public function onEnable()
    {
        $this->getLogger()->info("You're gay successfully !");
        self::$instance = $this;

        $this->registerEvents(); // Registering all events.

    }

    /*
     * Some additional functions :
     */

    public function registerEvents() : void {
        $eventsList = [
            new Compass()
        ];

        foreach ($eventsList as $events) {
            $this->getServer()->getPluginManager()->registerEvents($events, $this);
        }

    }

    public function getInstance() : Core {
        return self::$instance;
    }

}