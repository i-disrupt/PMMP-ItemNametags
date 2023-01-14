<?php

namespace kitsu\itemNameTag;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;

// objects & entities
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemMergeEvent;
use pocketmine\event\entity\ItemSpawnEvent;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this -> getLogger() -> info(TextFormat::GREEN . "Item Name Tag plugin has been enabled");
        $this -> saveDefaultConfig();
        $this -> getServer() -> getPluginManager() -> registerEvents($this, $this);
    }

    public function assignTag(ItemEntity $entity, int $count): void {
        $i = $entity -> getItem();
        $format = $this -> getConfig() -> get('format');
        $replace = ["{name}" => $i -> getName(), "{vanilla}" => $i -> getVanillaName(), "{count}" => $count, "{description}" => implode(TextFormat::EOL, $i -> getLore())];
        $format = str_replace(array_keys($replace), array_values($replace), strval($format));
        $entity -> setNameTag(TextFormat::colorize($format));
        $entity -> setNameTagAlwaysVisible();
    }

    public function onItemSpawn(ItemSpawnEvent $e): void {
        $entity = $e -> getEntity();
        $this -> assignTag($entity, $entity -> getItem() -> getCount());
    }

    public function onItemMerge(ItemMergeEvent $e): void {
        $entity = $e -> getEntity();
        $target = $e -> getTarget();

        if ($entity instanceof ItemEntity) {
            $c = $entity->getItem() -> getCount() + $target -> getItem() -> getCount();
            $this -> assignTag($target, $c);
        }
    }
}
