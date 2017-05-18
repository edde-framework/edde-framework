<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Store is intelligent key-value implementation with support of "transactions" and locking.
	 *
	 * This class is intended to be simple and effective to use and persist simple pieces of data; it
	 * could be used for sessions, cache or just small pieces which is not necessary to save in database.
	 *
	 * The store itself is able somehow emulate database in really small applications.
	 */
	interface IStore extends IConfigurable {
		/**
		 * lock the given key or whole store; if block is set to false and lock cannot be ackquired, exception should
		 * be thrown
		 *
		 * @param string|null $name
		 * @param bool        $block
		 *
		 * @return IStore
		 */
		public function lock(string $name = null, bool $block = true): IStore;

		/**
		 * unlock the given key or whole store
		 *
		 * @param string|null $name
		 *
		 * @return IStore
		 */
		public function unlock(string $name = null): IStore;

		/**
		 * if there is need to explicitly kill a lock created by another thread
		 *
		 * @param string|null $name
		 *
		 * @return IStore
		 */
		public function kill(string $name = null): IStore;

		/**
		 * is the store itself or the given key locked?
		 *
		 * @param string|null $name
		 *
		 * @return bool
		 */
		public function isLocked(string $name = null): bool;

		/**
		 * store a given value
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IStore
		 */
		public function set(string $name, $value): IStore;

		/**
		 * before value is set, lock is applied, value is set and lock is released
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IStore
		 */
		public function setExclusive(string $name, $value): IStore;

		/**
		 * get a data from the store
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function get(string $name, $default = null);
	}
