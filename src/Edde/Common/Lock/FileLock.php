<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\File\IFile;
	use Edde\Api\Lock\ILock;
	use Edde\Api\Lock\LazyLockDirectoryTrait;

	class FileLock extends AbstractLock {
		use LazyLockDirectoryTrait;
		/**
		 * @var IFile
		 */
		protected $file;

		/**
		 * @inheritdoc
		 */
		protected function onLock(): ILock {
			if ($this->locked()) {
				throw new LockedException(sprintf('The name (id) [%s] is already locked.', $this->getId()));
			}
			$this->file = $this->getLockFile()->touch();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function onUnlock(): ILock {
			$this->getLockFile()->delete();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function locked(): bool {
			return $this->getLockFile()->isAvailable();
		}

		protected function getLockFile(): IFile {
			return $this->lockDirectory->file(sha1($this->getId()) . '.lock');
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			$this->lockDirectory->create();
		}
	}
