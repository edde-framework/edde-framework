<?php
	namespace Edde\Common\RuntimeTest;

	use Edde\Common\Cache\AbstractCacheStorage;

	class DummyCacheStorage extends AbstractCacheStorage {
		public function save($id, $save) {
		}

		public function load($id) {
		}

		public function invalidate() {
		}

		protected function prepare() {
		}
	}
