<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Store\LazyStoreDirectoryTrait;

	class FileStore extends AbstractStore {
		use LazyStoreDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->storeDirectory->create();
		}
	}
