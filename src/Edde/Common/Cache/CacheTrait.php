<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\CacheException;
	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var ICache
		 */
		protected $cache;

		public function injectCacheFactory(ICacheFactory $cacheFactory) {
			$this->cacheFactory = $cacheFactory;
		}

		protected function cache() {
			if ($this->cacheFactory === null) {
				throw new CacheException(sprintf('Cache factory has not been injected into class [%s]; cannot use cache.', static::class));
			}
			$this->cache = $this->cacheFactory->factory(static::class);
		}
	}
