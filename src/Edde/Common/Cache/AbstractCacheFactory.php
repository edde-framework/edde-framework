<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\AbstractObject;

	/**
	 * Common stuff for a cache factory implementation.
	 */
	abstract class AbstractCacheFactory extends AbstractObject implements ICacheFactory {
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
		public function factory(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			return new Cache($cacheStorage ?: $this->cacheStorage, $this->namespace . $namespace);
		}
	}
