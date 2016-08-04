<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;

	/**
	 * Every storable object must be formally marked by this interface.
	 */
	interface IStorable extends ICrate {
	}
