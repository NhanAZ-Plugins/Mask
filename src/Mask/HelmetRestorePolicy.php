<?php

declare(strict_types=1);

namespace Mask;

use pocketmine\item\Item;

final class HelmetRestorePolicy{
	public static function shouldApplyMask(Item $helmet, ?Item $snapshot, bool $hasSnapshot, bool $isManagedMask) : bool{
		if($isManagedMask){
			return true;
		}
		if(!$hasSnapshot){
			return true;
		}
		if($snapshot === null){
			return false;
		}

		return $helmet->isNull() || $helmet->equals($snapshot);
	}

	public static function decideOffAction(Item $helmet, Item $snapshot, bool $isManagedMask, bool $canStoreSnapshot) : HelmetRestoreDecision{
		if($isManagedMask || $helmet->isNull() || $helmet->equals($snapshot)){
			return HelmetRestoreDecision::RESTORE_HELMET;
		}
		if($snapshot->isNull()){
			return HelmetRestoreDecision::DISCARD_EMPTY_SNAPSHOT;
		}
		if($canStoreSnapshot){
			return HelmetRestoreDecision::RETURN_TO_INVENTORY;
		}
		return HelmetRestoreDecision::KEEP_ENABLED;
	}
}
