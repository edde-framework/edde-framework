<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	trait LazyLockHandlerTrait {
		/**
		 * @var ILockHandler
		 */
		protected $lockHandler;

		/**
		 * @param ILockHandler $lockHandler
		 */
		public function lazyLockHandler(ILockHandler $lockHandler) {
			$this->lockHandler = $lockHandler;
		}
	}
