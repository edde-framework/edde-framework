<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Simple in-memory cache (per-request).
	 */
	class InMemoryCacheStorage extends AbstractCacheStorage {
		private $storage;

		public function save($id, $save) {
			$this->usse();
			return $this->storage[$id] = $save;
		}

		public function load($id) {
			$this->usse();
			if (isset($this->storage[$id]) === false) {
				return null;
			}
			return $this->storage[$id];
		}

		public function invalidate() {
			$this->usse();
			$this->storage = [];
			return $this;
		}

		protected function prepare() {
		}
	}
