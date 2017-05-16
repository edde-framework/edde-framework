<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	/**
	 * Actual implementation of locking mechanism (for example file locking).
	 */
	interface ILockHandler {
		/**
		 * create lock over given id
		 *
		 * @param string $id
		 *
		 * @return ILock
		 */
		public function lock(string $id): ILock;

		/**
		 * check if the given id is already locked; this method is not safe to use because
		 * when true is returned is could not be actually true in subsequent code
		 *
		 * @param string $id
		 *
		 * @return bool
		 */
		public function isLocked(string $id): bool;

		/**
		 * unlock the given id
		 *
		 * @param string $id
		 *
		 * @return ILock
		 */
		public function unlock(string $id): ILock;

		/**
		 * @return ILock
		 */
		public function createLock(string $id): ILock;
	}
