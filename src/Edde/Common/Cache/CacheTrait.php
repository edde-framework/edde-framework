<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\LazyCacheFactoryTrait;
	use Edde\Common\Deffered\Event\OnPrepareEvent;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyCacheFactoryTrait;
		/**
		 * @var ICache
		 */
		protected $cache;

		/**
		 * ultimately long name to prevent clashes; this sould be called automagically
		 *
		 * @param OnPrepareEvent $onPrepareEvent
		 */
		public function eventCacheTraitOnPrepareEvent(OnPrepareEvent $onPrepareEvent) {
			$this->lazy('cache', function () {
				return $this->cacheFactory->factory(static::class);
			});
		}
	}
