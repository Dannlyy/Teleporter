<?php

namespace Teleporter\listener;

use Teleporter\Core;

use pocketmine\block\Air;
use pocketmine\block\Block;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityTeleportEvent;

use pocketmine\math\Vector3;
use pocketmine\math\VoxelRayTrace;

use pocketmine\level\Position;

use pocketmine\Player;

use pocketmine\item\Item;

class Compass implements Listener
{

    // Here you can adjust the max distance for the compass when you're selecting a position :

    private const MAX_CHECKING = 20; //20 blocks
    private $avoidTrigger = []; 

    /*
     * Handling the events :
     */

    public function onInteract(PlayerInteractEvent $event)
    {

        $player = $event->getPlayer();
        $block = $event->getBlock();

        if(!$player->isOp()) return false;
        if($player->getInventory()->getItemInHand()->getId() !== Item::COMPASS) return false;


        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR or $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {


            //To avoid trigger when you right click on blocks.

            if((isset($this->avoidTrigger[$player->getName()]) and $this->avoidTrigger[$player->getName()] < time()) or !isset($this->avoidTrigger[$player->getName()])) {
                $this->avoidTrigger[$player->getName()] = time() + 2;
            } else {
                return false;
            }


            $blockTarget = ($this->getTargetBlock($player) !== null ? $this->getTargetBlock($player) : $block);

            if($player->teleport($blockTarget) !== false) {

                $player->sendMessage(Core::PREFIX . "Vous avez été teleporté dans la direction laquelle vous voyez !");
                return true;

            } else {

                $player->sendMessage(Core::PREFIX . "§cSélectionnez un endroit plus proche pour se teleporter !");
                return false;

            }


        }

        return true;

    }

    // This event let us : Remove teleportation to 0, 0, 0 after using the compass in a far position :

    public function onTeleport(EntityTeleportEvent $event) {
        if(($event->getTo()->asVector3())->__toString() === (new Vector3(0,0,0))->__toString()) $event->setCancelled();
    }

    // This function let us : Teleport to the block the player is looking :

    public function getTargetBlock(Player $player) : ?Vector3 {

        $blockFound = $player->getTargetBlock(self::MAX_CHECKING);
        if(!($blockFound instanceof Air)) return ($blockFound->asVector3())->add(0, 1, 0);

        return null;

    }

}