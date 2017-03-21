<?php
	declare(strict_types=1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\LazyCacheManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyCacheManagerTrait;
		use LazyContainerTrait;
		/**
		 * @var ICache
		 */
		protected $cache;

		/** @noinspection ClassMethodNameMatchesFieldNameInspection */
		protected function cache(): ICache {
			if ($this->cache === null) {
				$this->cacheManager->setup();
				$this->cache = $this->cacheManager->cache(static::class);
			}
			return $this->cache;
		}
	}
