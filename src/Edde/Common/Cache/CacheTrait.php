<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\LazyCacheManagerTrait;
	use Edde\Common\Deffered\Event\OnDefferedEvent;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyCacheManagerTrait;
		/**
		 * @var ICache
		 */
		protected $cache;

		/** @noinspection PhpUnusedParameterInspection */
		/**
		 * ultimately long name to prevent clashes; this sould be called automagically
		 *
		 * @param $onDefferedEvent $onDefferedEvent
		 */
		public function eventCacheTraitOnPrepareEvent(OnDefferedEvent $onDefferedEvent) {
			$this->lazy('cache', function () {
				return $this->cacheManager->cache(static::class);
			});
		}
	}
