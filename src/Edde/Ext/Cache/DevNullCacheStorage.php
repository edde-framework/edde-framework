<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * If caching may be off use this storage.
	 */
	class DevNullCacheStorage extends AbstractCacheStorage {
		public function save($id, $save) {
			$this->usse();
			return $save;
		}

		public function load($id) {
			$this->usse();
		}

		public function invalidate() {
			$this->usse();
			return $this;
		}

		protected function prepare() {
		}
	}
