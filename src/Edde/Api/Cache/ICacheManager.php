<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	use Edde\Api\Deffered\IDeffered;

	interface ICacheManager extends ICache, IDeffered {
		/**
		 * create a new cache
		 *
		 * @param string|null $namespace
		 * @param ICacheStorage $cacheStorage
		 *
		 * @return ICache
		 */
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache;
	}
