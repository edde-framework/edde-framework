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
	}
