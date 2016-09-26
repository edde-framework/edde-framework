<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

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

		/**
		 * @param ICacheFactory $cacheFactory
		 */
		public function lazyCacheFactory(ICacheFactory $cacheFactory) {
			$this->cacheFactory = $cacheFactory;
		}

		public function cache() {
			$this->lazy('cache', function () {
				return $this->cacheFactory->factory(static::class);
			});
		}
	}
