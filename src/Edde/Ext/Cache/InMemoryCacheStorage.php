<?php
	declare(strict_types=1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\ICacheable;
	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Simple in-memory cache (per-request).
	 */
	class InMemoryCacheStorage extends AbstractCacheStorage implements ICacheable {
		protected $storage = [];

		/**
		 * @inheritdoc
		 */
		public function save(string $id, $save) {
			$this->write++;
			return $this->storage[$id] = $save;
		}

		/**
		 * @inheritdoc
		 */
		public function load($id) {
			/** @noinspection NotOptimalIfConditionsInspection */
			if (isset($this->storage[$id]) || array_key_exists($id, $this->storage)) {
				$this->hit++;
				return $this->storage[$id];
			}
			$this->miss++;
			return null;
		}

		/**
		 * @inheritdoc
		 */
		public function invalidate() {
			$this->storage = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function __sleep() {
			$this->storage = [];
			return parent::__sleep();
		}
	}
