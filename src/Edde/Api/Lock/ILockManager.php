<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Lock is a mechanism of general locking of an identifier. This feature must be reliable and
	 * also it must be able to create also dead-locks which are not possible with File locks.
	 *
	 * In general when one thread make a lock, another thread should see this id as locked.
	 */
	interface ILockManager extends IConfigurable {
		public function kill(string $id): ILockManager;
	}
