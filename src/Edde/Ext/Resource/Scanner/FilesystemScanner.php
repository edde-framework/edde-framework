<?php
	namespace Edde\Ext\Resource\Scanner;

	use Edde\Common\Resource\Resource;
	use Edde\Common\Resource\Scanner\AbstractScanner;
	use Edde\Common\Url\Url;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;
	use SplFileInfo;

	/**
	 * Simple filesystem scanner; it does not any caching, so it's usage is heavily suboptimal! Use only when needed and with cache.
	 */
	class FilesystemScanner extends AbstractScanner {
		/**
		 * path to scan
		 *
		 * @var string
		 */
		protected $path;

		/**
		 * @param string $path
		 */
		public function __construct($path) {
			$this->path = $path;
		}

		public function scan() {
			if (is_dir($this->path) === false) {
				return;
			}
			/** @var $splFileInfo SplFileInfo */
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				yield new Resource(Url::factory('file', (string)$splFileInfo));
			}
		}
	}
