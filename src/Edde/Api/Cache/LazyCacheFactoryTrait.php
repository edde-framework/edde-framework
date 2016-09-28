<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	/**
	 * Lazy cache factory dependency.
	 */
	trait LazyCacheFactoryTrait {
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;

		/**
		 * @param ICacheFactory $cacheFactory
		 */
		public function lazyCacheFactory(ICacheFactory $cacheFactory) {
			$this->cacheFactory = $cacheFactory;
		}
	}
