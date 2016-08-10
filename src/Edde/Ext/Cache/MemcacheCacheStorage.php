<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Common\Cache\AbstractCacheStorage;

	class MemcacheCacheStorage extends AbstractCacheStorage {
		/**
		 * @var \Memcache
		 */
		protected $memcache;
		/**
		 * @var array
		 */
		protected $serverList = [];

		public function addServer($server, $port = 11211) {
			$this->serverList[] = [
				$server,
				$port,
			];
			return $this;
		}

		public function save($id, $save) {
			$this->usse();
			$this->memcache->set($id, $save);
			return $this;
		}

		public function load($id) {
			$this->usse();
			return $this->memcache->get($id);
		}

		public function invalidate() {
			$this->usse();
			$this->memcache->flush();
		}

		protected function prepare() {
			$this->memcache = new \Memcache();
			foreach ($this->serverList as $item) {
				$this->memcache->addserver($item[0], $item[1]);
			}
		}
	}
