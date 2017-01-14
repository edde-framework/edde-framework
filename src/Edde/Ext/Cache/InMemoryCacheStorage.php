<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\ICacheable;
	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Simple in-memory cache (per-request).
	 */
	class InMemoryCacheStorage extends AbstractCacheStorage implements ICacheable {
		protected $storage;

		public function save(string $id, $save) {
			return $this->storage[$id] = $save;
		}

		public function load($id) {
			if (isset($this->storage[$id]) === false) {
				return null;
			}
			return $this->storage[$id];
		}

		public function invalidate() {
			$this->storage = [];
			return $this;
		}

		public function __sleep() {
			$this->storage = [];
			return parent::__sleep();
		}
	}
