<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * Lazy crate factory dependency.
	 */
	trait LazyCrateFactoryTrait {
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		/**
		 * @param ICrateFactory $crateFactory
		 */
		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}
	}
