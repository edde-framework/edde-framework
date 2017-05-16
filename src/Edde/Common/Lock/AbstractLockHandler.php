<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILock;
	use Edde\Api\Lock\ILockHandler;
	use Edde\Common\Object;

	class AbstractLockHandler extends Object implements ILockHandler {
		public function lock(string $id): ILock {
		}

		public function isLocked(string $id): bool {
		}

		public function unlock(string $id): ILock {
		}

		public function createLock(string $id): ILock {
		}
	}
