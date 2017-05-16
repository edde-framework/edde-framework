<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILockManager;
	use Edde\Api\Lock\LazyLockHandlerTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class LockManager extends Object implements ILockManager {
		use LazyLockHandlerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function kill(string $id): ILockManager {
			return $this;
		}
	}
