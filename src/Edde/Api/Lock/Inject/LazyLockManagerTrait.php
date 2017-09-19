<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock\Inject;

	use Edde\Api\Lock\ILockManager;

	trait LazyLockManagerTrait {
		/**
		 * @var ILockManager
		 */
		protected $lockManager;

		/**
		 * @param ILockManager $lockManager
		 */
		public function lazyLockManager(ILockManager $lockManager) {
			$this->lockManager = $lockManager;
		}
	}
