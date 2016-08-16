<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\CacheStorageException;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Common\Cache\AbstractCacheStorage;

	class FileCacheStorage extends AbstractCacheStorage {
		/**
		 * @var ICacheDirectory
		 */
		protected $cacheDirectory;
		/**
		 * @var string
		 */
		protected $namespace;

		public function __construct(ICacheDirectory $cacheDirectory, string $namespace = '') {
			$this->cacheDirectory = $cacheDirectory;
			$this->namespace = $namespace;
		}

		public function save($id, $save) {
			$this->use();
			$file = $this->file($id);
			if ($save === null) {
				if (@unlink($file) === false) {
					throw new CacheStorageException(sprintf('Cannot remove cached file [%s] for cache id [%s] from folder [%s].', $file, $id, $this->cacheDirectory));
				}
				return $save;
			}
			if (($handle = @fopen($file, 'c+b')) === false) {
				throw new CacheStorageException(sprintf('Cannot write to the cache file [%s]. Please check cache folder [%s] permissions.', $file, $this->cacheDirectory));
			}
			ftruncate($handle, 0);
			fwrite($handle, serialize($save));
			fclose($handle);
			return $save;
		}

		protected function file($id) {
			return sprintf('%s/%s', $this->cacheDirectory, sha1($this->namespace . $id));
		}

		public function load($id) {
			$this->use();
			if (($handle = @fopen($this->file($id), 'r+b')) === false) {
				return null;
			}
			$source = unserialize(stream_get_contents($handle));
			fclose($handle);
			return $source;
		}

		public function invalidate() {
			$this->use();
			$this->cacheDirectory->purge();
		}

		protected function prepare() {
			$this->cacheDirectory->create();
		}
	}
