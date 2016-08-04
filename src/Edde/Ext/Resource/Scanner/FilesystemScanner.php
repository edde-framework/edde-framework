<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource\Scanner;

	use Edde\Api\File\IDirectory;
	use Edde\Common\Resource\Scanner\AbstractScanner;

	/**
	 * Simple filesystem scanner; it does not any caching, so it's usage is heavily suboptimal! Use only when needed and with cache.
	 */
	class FilesystemScanner extends AbstractScanner {
		/**
		 * path to scan
		 *
		 * @var IDirectory
		 */
		protected $directory;

		public function __construct(IDirectory $directory) {
			$this->directory = $directory;
		}

		public function scan() {
			if ($this->directory->exists() === false) {
				return [];
			}
			return $this->directory->getIterator();
		}
	}
