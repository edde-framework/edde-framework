<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\LazyCacheDirectoryTrait;
	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Cache is stored in one file based on a storage namespace.
	 */
	class FlatCacheStorage extends AbstractCacheStorage {
		use LazyCacheDirectoryTrait;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var string
		 */
		protected $file;

		/**
		 * Two flies are sitting on a pile of dog poop. One suggests to the other: “Do you want to hear a really good joke?”
		 *
		 * The other fly replies: “But nothing disgusting like last time, I’m trying to eat here!”
		 *
		 * @param string $namespace
		 */
		public function __construct(string $namespace = null) {
			$this->namespace = $namespace;
		}

		protected function get() {
			return @unserialize(($content = file_get_contents($this->file)) ? $content : '');
		}

		public function save(string $id, $save) {
			$this->use();
			$this->write++;
			touch($this->file);
			$source = $this->get();
			$source[$id] = $save;
			file_put_contents($this->file, serialize($source));
			return $save;
		}

		public function load($id) {
			$this->use();
			$source = $this->get();
			if (isset($source[$id])) {
				$this->hit++;
				return $source[$id];
			}
			$this->miss++;
			return null;
		}

		public function invalidate() {
			$this->use();
			unlink($this->file);
		}

		protected function prepare() {
			parent::prepare();
			$this->cacheDirectory->create();
			$this->file = $this->cacheDirectory->filename(sha1($this->namespace));
		}
	}
