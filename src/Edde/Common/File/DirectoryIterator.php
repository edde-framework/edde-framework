<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IDirectoryIterator;
	use Edde\Common\AbstractObject;

	class DirectoryIterator extends AbstractObject implements IDirectoryIterator {
		/**
		 * @var IDirectory[]
		 */
		protected $directoryList = [];

		public function addDirectory(IDirectory $directory): IDirectoryIterator {
			$this->directoryList[$directory->getDirectory()] = $directory;
			return $this;
		}

		public function getIterator() {
			foreach ($this->directoryList as $directory) {
				foreach ($directory as $file) {
					yield $file;
				}
			}
		}
	}
