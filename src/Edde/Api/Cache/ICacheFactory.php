<?php
	namespace Edde\Api\Cache;

	interface ICacheFactory {
		/**
		 * @param string|null $namespace
		 * @param ICacheStorage $cacheStorage
		 *
		 * @return ICache
		 */
		public function factory($namespace = null, ICacheStorage $cacheStorage = null);
	}
