<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\AbstractObject;

	/**
	 * Common stuff for a cache cache implementation.
	 */
	abstract class AbstractCacheManager extends AbstractObject implements ICacheManager {
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;

		/**
		 * @param string $namespace
		 * @param ICacheStorage $cacheStorage
		 */
		public function __construct(string $namespace, ICacheStorage $cacheStorage) {
			$this->namespace = $namespace;
			$this->cacheStorage = $cacheStorage;
		}

		/**
		 * @inheritdoc
		 */
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			return new Cache($cacheStorage ?: $this->cacheStorage, $this->namespace . $namespace);
		}
	}
