<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IDirectoryIterator;
	use Edde\Common\Usable\AbstractUsable;

	class DirectoryIterator extends AbstractUsable implements IDirectoryIterator {
		/**
		 * @var IDirectory[]
		 */
		protected $directoryList = [];

		public function addDirectory(IDirectory $directory): IDirectoryIterator {
			$this->directoryList[$directory->getDirectory()] = $directory;
			return $this;
		}

		public function getIterator() {
			$this->use();
			foreach ($this->directoryList as $directory) {
				foreach ($directory as $file) {
					yield $file;
				}
			}
		}

		protected function prepare() {
		}
	}
