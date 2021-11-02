<?php

namespace famima65536\chickenrider;

use famima65536\chickenrider\entity\RideableChicken;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Loader extends PluginBase {
	public function onLoad(): void{
		/** @var EntityFactory $factory */
		$factory = EntityFactory::getInstance();
		$factory->register(RideableChicken::class, function(World $world, CompoundTag $nbt): RideableChicken{
			return new RideableChicken(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		},["Rideable Chicken"]);

		/** @var ItemFactory $factory */
		$factory = ItemFactory::getInstance();
		$factory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::CHICKEN), "Rideable Chicken Spawn Egg") extends SpawnEgg{
			public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : RideableChicken{
				return new RideableChicken(Location::fromObject($pos, $world, $yaw, $pitch));
			}
		});
	}

	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}
}