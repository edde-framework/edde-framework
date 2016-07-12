<?php
	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\CacheStorageException;
	use Edde\Common\Cache\AbstractCacheStorage;
	use Edde\Common\File\FileUtils;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;

	class FileCacheStorage extends AbstractCacheStorage {
		/**
		 * defaults to system temp dir
		 *
		 * @var string
		 */
		private $cacheDir;
		private $namespace;

		public function __construct($cacheDir = null, $namespace = null) {
			$this->cacheDir = $cacheDir;
			$this->namespace = $namespace;
		}

		public function save($id, $save) {
			$this->usse();
			$file = $this->file($id);
			if ($save === null) {
				if (@unlink($file) === false) {
					throw new CacheStorageException(sprintf('Cannot remove cached file [%s] for cache id [%s] from folder [%s].', $file, $id, $this->cacheDir));
				}
				return $save;
			}
			if (($handle = @fopen($file, 'c+b')) === false) {
				throw new CacheStorageException(sprintf('Cannot write to the cache file [%s]. Please check cache folder [%s] permissions.', $file, $this->cacheDir));
			}
			ftruncate($handle, 0);
			fwrite($handle, serialize($save));
			fclose($handle);
			return $save;
		}

		protected function file($id) {
			return sprintf('%s/%s', $this->cacheDir, sha1($id));
		}

		public function load($id) {
			$this->usse();
			if (($handle = @fopen($this->file($id), 'r+b')) === false) {
				return null;
			}
			$source = unserialize(stream_get_contents($handle));
			fclose($handle);
			return $source;
		}

		public function invalidate() {
			$this->usse();
			/** @var $splFileInfo \SplFileInfo */
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cacheDir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $splFileInfo) {
				FileUtils::delete($splFileInfo);
			}
			FileUtils::delete($splFileInfo);
		}

		protected function prepare() {
			$this->cacheDir = FileUtils::normalize(sprintf('%s/%s', $this->cacheDir ?: (sys_get_temp_dir() . '/edde'), sha1($this->namespace ?: __DIR__)));
			FileUtils::createDir($this->cacheDir, 0777);
		}
	}
