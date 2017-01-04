<?php
	declare(strict_types = 1);

	namespace Edde\Api\Serialize;

	/**
	 * Marker interface for objects which would go to HashIndex when serialized (to maintain object references between different serializations).
	 */
	interface IHashable {
	}
