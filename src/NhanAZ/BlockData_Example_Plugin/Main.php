<?php

declare(strict_types=1);

namespace NhanAZ\BlockData_Example_Plugin;

use NhanAZ\BlockData\BlockData;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

final class Main extends PluginBase implements Listener {

	protected BlockData $blockdata;

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->blockdata = new BlockData($this);
	}

	/**
	 * @param PlayerInteractEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerInteract(PlayerInteractEvent $event): void {
		if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $event->getItem()->equals(VanillaItems::POTATO())) {
			$block = $event->getBlock();
			if ($block->isSameType(VanillaBlocks::GLASS())) {
				$data = $this->blockdata->getData($block); /* "durability:4" */
				$data = explode(":", $data); /* $data[0] = "durability" * $data[1] = 4 */
				$event->getPlayer()->sendMessage(TextFormat::LIGHT_PURPLE . "Durability of this block is: " . $data[1]);
			}
		}
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @priority HIGH
	 */
	public function onBlockPlace(BlockPlaceEvent $event): void {
		$block = $event->getBlock();
		if ($block->isSameType(VanillaBlocks::GLASS())) {
			$this->blockdata->setData($block, "durability:4");
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority HIGH
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		$block = $event->getBlock();
		if ($block->isSameType(VanillaBlocks::GLASS())) {
			$data = $this->blockdata->getData($block); /* "durability:4" */
			if ($data !== null) {
				$data = explode(":", $data); /* $data[0] = "durability" * $data[1] = 4 */
				$durability = $data[1];
				if ($durability > 1) {
					$event->cancel();
					$durability = (int) $durability - 1;
					$this->blockdata->setData($block, "durability:{$durability}");
				}
			}
		}
	}
}
