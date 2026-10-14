<?php

declare(strict_types=1);

namespace Mask;

enum HelmetRestoreDecision{
	case RESTORE_HELMET;
	case RETURN_TO_INVENTORY;
	case DISCARD_EMPTY_SNAPSHOT;
	case KEEP_ENABLED;
}
