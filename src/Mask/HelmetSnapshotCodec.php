<?php

declare(strict_types=1);

namespace Mask;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

final class HelmetSnapshotCodec{
	public static function encode(Item $item) : string{
		if($item->isNull()){
			return "air";
		}
		$root = new TreeRoot($item->nbtSerialize());
		return "nbt:" . base64_encode((new LittleEndianNbtSerializer())->write($root));
	}

	public static function decode(string $encoded) : Item{
		if($encoded === "air"){
			return VanillaItems::AIR();
		}
		if(!str_starts_with($encoded, "nbt:")){
			throw new \UnexpectedValueException("Snapshot format is not recognized.");
		}

		$binary = base64_decode(substr($encoded, 4), true);
		if($binary === false){
			throw new \UnexpectedValueException("Snapshot is not valid base64.");
		}

		$tag = (new LittleEndianNbtSerializer())->read($binary)->mustGetCompoundTag();
		return Item::nbtDeserialize($tag);
	}
}
