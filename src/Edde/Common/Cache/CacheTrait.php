<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\LazyCacheFactoryTrait;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyCacheFactoryTrait;
		/**
		 * @var ICache
		 */
		protected $cache;

		public function cache() {
			$this->lazy('cache', function () {
				return $this->cacheFactory->factory(static::class);
			});
		}
	}
