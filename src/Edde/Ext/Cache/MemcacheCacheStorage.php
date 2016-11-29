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

		public function save(string $id, $save) {
			$this->use();
			$this->memcache->set($id, $save);
			return $this;
		}

		public function load($id) {
			$this->use();
			return ($item = $this->memcache->get($id)) === false ? null : $item;
		}

		public function invalidate() {
			$this->use();
			$this->memcache->flush();
		}

		protected function onBootstrap() {
			parent::onBootstrap();
			$this->memcache = new \Memcache();
			foreach ($this->serverList as $item) {
				$this->memcache->addServer($item[0], $item[1]);
			}
		}
	}
