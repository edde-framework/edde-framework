<?php
	namespace Edde\Common\File;

	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\IDirectory;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Usable\AbstractUsable;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;

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

		public function getFileList() {
			$this->usse();
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				yield $path;
			}
		}

		public function save($name, $content) {
			$this->usse();
			file_put_contents($file = ($this->directory . '/' . $name), $content);
			return new FileResource(FileUtils::url($file));
		}

		public function get($file) {
			return file_get_contents(FileUtils::realpath($this->filename($file)));
		}

		public function filename($file) {
			return FileUtils::normalize($this->getDirectory() . '/' . $file);
		}

		public function getDirectory() {
			$this->usse();
			return $this->directory;
		}

		public function make() {
			if (is_dir($this->directory) === false && @mkdir($this->directory, 0777, true) && is_dir($this->directory) === false) {
				throw new DirectoryException(sprintf('Cannot create directory [%s].', $this->directory));
			}
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		public function purge() {
			FileUtils::recreate($this->directory);
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		public function delete() {
			$this->usse();
			FileUtils::delete($this->directory);
		}

		public function exists() {
			return is_dir($this->directory);
		}

		public function getIterator() {
			$this->usse();
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				yield new FileResource((string)$splFileInfo);
			}
		}

		public function __toString() {
			return $this->directory;
		}

		protected function prepare() {
			$this->directory = FileUtils::realpath($this->directory);
		}
	}
