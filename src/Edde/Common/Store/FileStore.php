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
		public function set(string $id, $value): IStore {
			$this->getFile($id)->save((string)$value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $id, $default = null) {
			$file = $this->getFile($id);
			if ($file->isAvailable() === false) {
				return $default;
			}
			return $file->get();
		}

		protected function getFile(string $id): IFile {
			if (isset($this->fileList[$id])) {
				return $this->fileList[$id];
			}
			$list = explode('-', $this->cryptEngine->guid($id));
			$file = array_pop($list) . '.store';
			return $this->fileList[$id] = $this->storeDirectory->directory(implode('/', $list))->create()->file($file);
		}

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->storeDirectory->create();
		}
	}
