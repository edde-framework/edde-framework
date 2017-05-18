<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Lock\ILock;

	class FileLockManager extends AbstractLockManager {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function lock(string $id): ILock {
			return $this->createLock($id)->lock();
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $id): ILock {
			return $this->createLock($id)->unlock();
		}

		/**
		 * @inheritdoc
		 */
		public function kill(string $id): ILock {
			return $this->createLock($id)->kill();
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(string $id): bool {
			return $this->createLock($id)->isLocked();
		}

		/**
		 * @inheritdoc
		 */
		public function createLock(string $id): ILock {
			return $this->lockList[$id] ?? $this->lockList[$id] = $this->container->create(FileLock::class, [
					$id,
				])->setup();
		}
	}
