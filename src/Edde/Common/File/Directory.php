<?php
	namespace Edde\Common\File;

	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\IDirectory;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * Representation of directory on the filesystem.
	 */
	class Directory extends AbstractUsable implements IDirectory {
		/**
		 * @var string
		 */
		protected $directory;

		/**
		 * @param string $directory
		 */
		public function __construct($directory) {
			$this->directory = $directory;
		}

		public function getDirectory() {
			$this->usse();
			return $this->directory;
		}

		public function getFileList() {
			$this->usse();
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				yield $path;
			}
		}

		public function file($name, $content) {
			$this->usse();
			file_put_contents($this->directory . '/' . $name, $content);
			return $this;
		}

		public function make() {
			if (is_dir($this->directory) === false && @mkdir($this->directory, 0777, true) && is_dir($this->directory) === false) {
				throw new DirectoryException(sprintf('Cannot create directory [%s].', $this->directory));
			}
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		public function __toString() {
			return $this->directory;
		}

		protected function prepare() {
			$this->directory = FileUtils::realpath($this->directory);
		}
	}
