<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\File\IFile;
	use Edde\Api\Store\IStore;
	use Edde\Api\Store\LazyStoreDirectoryTrait;

	class FileStore extends AbstractStore {
		use LazyStoreDirectoryTrait;
		/**
		 * @var IFile[]
		 */
		protected $lockList = [];

		/**
		 * @inheritdoc
		 */
		public function lock(string $name = null, bool $block = true): IStore {
			$lock = $this->getLockFile($name);
			$lock->lock(true, $block);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name = null): IStore {
			$lock = $this->getLockFile($name);
			$lock->unlock();
			unset($this->lockList[$name]);
			return $this;
		}

		protected function getLockFile(string $name = null): IFile {
			return $this->lockList[$name] ?? $this->lockList[$name] = $this->storeDirectory->file('.lock' . ($name ? '-' . $name : ''));
		}

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->storeDirectory->create();
		}
	}
