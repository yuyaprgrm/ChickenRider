<?php

namespace famima65536\chickenrider;

use famima65536\chickenrider\entity\RideableChicken;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\player\Player;

class EventListener implements Listener {

	/** @var array<int,RideableChicken> */
	private array $rider_to_chicken_table = [];

	public function onEntityDamageByEntity(EntityDamageByEntityEvent $event):void{
		$entity = $event->getEntity();
		$damager = $event->getDamager();
		if($entity instanceof RideableChicken and $damager instanceof Player){
			$entity->riddenBy($damager);
			$this->rider_to_chicken_table[$damager->getId()] = $entity;
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event):void{
		$pk = $event->getPacket();
		if(!$pk instanceof PlayerInputPacket){
			return;
		}

		$player = $event->getOrigin()->getPlayer();
		if($player === null){
			return;
		}

		$chicken = $this->rider_to_chicken_table[$player->getId()] ?? null;
		if($chicken === null){
			return;
		}

		$directionPlane = $chicken->getDirectionPlane();
		$directionPlane2 = new Vector2($directionPlane->y, -$directionPlane->x);

		$motionPlane = $directionPlane->multiply($pk->motionY)->addVector($directionPlane2->multiply($pk->motionX))->multiply(0.7);
		$player->sendTip("{$motionPlane->x}:{$motionPlane->y}");
		$motion = (new Vector3($motionPlane->x, 0, $motionPlane->y));
		$chicken->setMotion($motion);

	}
}