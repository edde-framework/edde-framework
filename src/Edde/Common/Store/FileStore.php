<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Store\IStore;
	use Edde\Api\Store\LazyStoreDirectoryTrait;

	class FileStore extends AbstractStore {
		use LazyStoreDirectoryTrait;
		use LazyCryptEngineTrait;
		/**
		 * @var IFile[]
		 */
		protected $fileList = [];

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IStore {
			$this->getFile($name)->save(serialize($value));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			$file = $this->getFile($name);
			if ($file->isAvailable() === false) {
				return $default;
			}
			return unserialize($file->get());
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return $this->getFile($name)->isAvailable();
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IStore {
			$this->getFile($name)->delete();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function drop(): IStore {
			$this->storeDirectory->purge();
			return $this;
		}

		protected function getFile(string $name): IFile {
			if (isset($this->fileList[$name])) {
				return $this->fileList[$name];
			}
			$list = explode('-', $this->cryptEngine->guid($name));
			$file = array_pop($list) . '.store';
			return $this->fileList[$name] = $this->storeDirectory->directory(implode('/', $list))->create()->file($file);
		}

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->storeDirectory->create();
		}
	}
