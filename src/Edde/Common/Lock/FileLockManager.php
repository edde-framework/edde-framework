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
		public function lock(string $id, string $source = null): ILock {
			return $this->createLock($id, $source)->lock();
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $id, string $source = null): ILock {
			return $this->createLock($id, $source)->unlock();
		}

		/**
		 * @inheritdoc
		 */
		public function kill(string $id, string $source = null): ILock {
			return $this->createLock($id, $source)->kill();
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(string $id, string $source = null): bool {
			return $this->createLock($id, $source)->isLocked();
		}

		/**
		 * @inheritdoc
		 */
		public function createLock(string $id, string $source = null): ILock {
			return $this->lockList[$id] ?? $this->lockList[$id] = $this->container->create(FileLock::class, [
					$id,
					$source,
				])->setup();
		}
	}
