<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Common\Container\LazyInjectTrait;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyInjectTrait;
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var ICache
		 */
		protected $cache;

		public function lazyCacheFactory(ICacheFactory $cacheFactory) {
			$this->cacheFactory = $cacheFactory;
		}

		protected function lazyList(): array {
			return [
				'cache' => function () {
					return $this->cacheFactory->factory(static::class);
				},
			];
		}

		protected function cache() {
			$this->cache = $this->cacheFactory->factory(static::class);
		}
	}
