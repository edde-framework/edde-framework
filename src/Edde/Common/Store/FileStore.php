<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Store\IStore;

	class FileStore extends AbstractStore {
		/**
		 * @inheritdoc
		 */
		public function lock(string $name = null, bool $block = true): IStore {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name = null): IStore {
			return $this;
		}
	}
