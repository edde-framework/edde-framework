<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Common stuff for a cache cache implementation.
	 */
	abstract class AbstractCacheManager extends AbstractCache implements ICacheManager {
		use DefferedTrait;
		/**
		 * @inheritdoc
		 */
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			return new Cache($cacheStorage ?: $this->cacheStorage, $this->namespace . $namespace);
		}
	}
