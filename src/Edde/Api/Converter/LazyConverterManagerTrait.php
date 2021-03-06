<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	/**
	 * Lazy dependency on a converter manager.
	 */
	trait LazyConverterManagerTrait {
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		/**
		 * @param IConverterManager $converterManager
		 */
		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}
	}
