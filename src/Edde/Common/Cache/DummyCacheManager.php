<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Ext\Cache\DevNullCacheStorage;

	class DummyCacheManager extends CacheManager {
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			return new Cache(new DevNullCacheStorage());
		}
	}
