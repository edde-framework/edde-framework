<?php
	use Edde\Common\Cache\AbstractCacheStorage;

	class TestCacheStorage extends AbstractCacheStorage {
		private $cache;

		public function save($id, $save) {
			$this->cache[$id] = $save;
			return $save;
		}

		public function load($id) {
			return isset($this->cache[$id]) ? $this->cache[$id] : null;
		}

		public function invalidate() {
			$this->cache = [];
			return $this;
		}

		protected function prepare() {
		}
	}
