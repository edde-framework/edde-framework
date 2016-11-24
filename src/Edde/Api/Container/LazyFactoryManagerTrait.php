<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	trait LazyFactoryManagerTrait {
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;

		public function lazyFactoryManager(IFactoryManager $factoryManager) {
			$this->factoryManager = $factoryManager;
		}
	}
