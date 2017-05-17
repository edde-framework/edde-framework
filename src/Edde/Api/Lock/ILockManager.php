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
		/**
		 * create a new lock over the given id
		 *
		 * @param string      $id
		 * @param string|null $source
		 *
		 * @return ILock
		 */
		public function lock(string $id, string $source = null): ILock;

		/**
		 * unlocks the given id
		 *
		 * @param string      $id
		 * @param string|null $source
		 *
		 * @return ILock
		 */
		public function unlock(string $id, string $source = null): ILock;

		/**
		 * kill a lock of the given id (use wisely!)
		 *
		 * @param string      $id
		 * @param string|null $source
		 *
		 * @return ILock
		 */
		public function kill(string $id, string $source = null): ILock;

		/**
		 * this method is not reliable as the time between "isLocked()" and eventual "lock()"
		 * is not handled and another thread could in meanwhile create a lock
		 *
		 * @param string      $id
		 * @param string|null $source
		 *
		 * @return bool
		 */
		public function isLocked(string $id, string $source = null): bool;

		/**
		 * only creates a lock without any further action
		 *
		 * @param string      $id
		 * @param string|null $source
		 *
		 * @return ILock
		 */
		public function createLock(string $id, string $source = null): ILock;
	}
