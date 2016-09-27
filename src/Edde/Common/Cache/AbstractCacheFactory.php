<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\AbstractObject;

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
		public function __construct($namespace, ICacheStorage $cacheStorage) {
			$this->namespace = $namespace;
			$this->cacheStorage = $cacheStorage;
		}

		/**
		 * @inheritdoc
		 */
		public function factory($namespace = null, ICacheStorage $cacheStorage = null) {
			return new Cache($cacheStorage ?: $this->cacheStorage, $this->namespace . $namespace);
		}
	}
