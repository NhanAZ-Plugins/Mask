<?php

declare(strict_types=1);

use Mask\HelmetRestoreDecision;
use Mask\HelmetRestorePolicy;
use Mask\HelmetSnapshotCodec;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

$autoload = getenv("AXOLOTL_AUTOLOAD");
if(!is_string($autoload) || $autoload === ""){
	fwrite(STDERR, "Set AXOLOTL_AUTOLOAD to an Axolotl-PM API 5 autoloader.\n");
	exit(2);
}
require $autoload;

require __DIR__ . "/../src/Mask/HelmetSnapshotCodec.php";
require __DIR__ . "/../src/Mask/HelmetRestoreDecision.php";
require __DIR__ . "/../src/Mask/HelmetRestorePolicy.php";

function assertTrue(bool $condition, string $message) : void{
	if(!$condition){
		throw new RuntimeException($message);
	}
}

$original = VanillaItems::IRON_HELMET()->setCustomName("Preserve this helmet");
$original->getNamedTag()->setString("example:custom", "metadata");
$decoded = HelmetSnapshotCodec::decode(HelmetSnapshotCodec::encode($original));
assertTrue($decoded->equals($original), "Snapshot round-trip must preserve item and named metadata.");

$air = VanillaItems::AIR();
$decodedAir = HelmetSnapshotCodec::decode(HelmetSnapshotCodec::encode($air));
assertTrue($decodedAir->isNull(), "An empty original helmet slot must survive a snapshot round-trip.");

$invalidRejected = false;
try{
	HelmetSnapshotCodec::decode("not-base64!");
}catch(UnexpectedValueException){
	$invalidRejected = true;
}
assertTrue($invalidRejected, "Invalid snapshot data must be rejected instead of treated as an empty item.");

$newHelmet = VanillaItems::DIAMOND_HELMET();
assertTrue(
	HelmetRestorePolicy::decideOffAction($newHelmet, $original, false, true) === HelmetRestoreDecision::RETURN_TO_INVENTORY,
	"A changed helmet must remain equipped while the saved original is returned to available inventory space."
);
assertTrue(
	HelmetRestorePolicy::decideOffAction($newHelmet, $original, false, false) === HelmetRestoreDecision::KEEP_ENABLED,
	"A full inventory must not discard the saved helmet or turn the mode off."
);
assertTrue(
	HelmetRestorePolicy::decideOffAction($newHelmet, $air, false, false) === HelmetRestoreDecision::DISCARD_EMPTY_SNAPSHOT,
	"An empty original snapshot must not remove a replacement helmet."
);
assertTrue(
	HelmetRestorePolicy::decideOffAction($newHelmet, $original, true, false) === HelmetRestoreDecision::RESTORE_HELMET,
	"A plugin-managed mask may be replaced with the saved helmet."
);
assertTrue(
	HelmetRestorePolicy::shouldApplyMask($newHelmet, $original, true, false) === false,
	"The scheduled mask must not overwrite a player-selected helmet."
);
assertTrue(
	HelmetRestorePolicy::shouldApplyMask(VanillaItems::AIR(), $original, true, false),
	"An empty helmet slot may be filled while preserving its saved original."
);
assertTrue(
	HelmetRestorePolicy::shouldApplyMask($newHelmet, null, true, false) === false,
	"An unreadable snapshot must fail closed."
);

$headTypes = [
	MobHeadType::SKELETON(),
	MobHeadType::WITHER_SKELETON(),
	MobHeadType::ZOMBIE(),
	MobHeadType::PLAYER(),
	MobHeadType::CREEPER(),
	MobHeadType::DRAGON()
];
$headStates = [];
foreach($headTypes as $headType){
	$head = VanillaBlocks::MOB_HEAD()->setMobHeadType($headType)->asItem();
	$encodedHead = HelmetSnapshotCodec::encode($head);
	$headStates[$encodedHead] = true;
	assertTrue(HelmetSnapshotCodec::decode($encodedHead)->equals($head), "Mob-head subtype must survive snapshot serialization.");
}
assertTrue(count($headStates) === 6, "Each of the six legacy mask variants must remain distinct in API 5.");

fwrite(STDOUT, "Mask API 5 snapshot and inventory safety fixtures passed.\n");
