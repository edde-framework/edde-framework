<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	interface IStoreManager extends IStore {
		/**
		 * register a given store; if the name is not provided, get_class($store) should be used
		 *
		 * @param IStore $store
		 * @param string $name
		 *
		 * @return IStoreManager
		 */
		public function registerStore(IStore $store, string $name = null): IStoreManager;

		/**
		 * select a store with the given name as current
		 *
		 * @param string $name
		 *
		 * @return IStoreManager
		 */
		public function select(string $name): IStoreManager;
	}
