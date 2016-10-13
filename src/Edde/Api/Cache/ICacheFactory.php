<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	interface ICacheFactory {
		/**
		 * @param string|null $namespace
		 * @param ICacheStorage $cacheStorage
		 *
		 * @return ICache
		 */
		public function factory(string $namespace = null, ICacheStorage $cacheStorage = null): ICache;
	}
