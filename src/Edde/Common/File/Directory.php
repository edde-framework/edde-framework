<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IFile;
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
			$this->use();
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				yield $path;
			}
		}

		public function save($name, $content) {
			$this->use();
			file_put_contents($file = ($this->directory . '/' . $name), $content);
			return new File(FileUtils::url($file));
		}

		public function get($file) {
			return file_get_contents(FileUtils::realpath($this->filename($file)));
		}

		public function filename($file) {
			return FileUtils::normalize($this->getDirectory() . '/' . $file);
		}

		public function getDirectory() {
			$this->use();
			return $this->directory;
		}

		public function file(string $file): IFile {
			return new File($this->filename($file));
		}

		public function create() {
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
			$this->use();
			FileUtils::delete($this->directory);
		}

		public function exists() {
			return is_dir($this->directory);
		}

		public function directory($directory): IDirectory {
			return new Directory($this->getDirectory() . '/' . $directory);
		}

		public function parent(): IDirectory {
			return new Directory($this->getDirectory() . '/..');
		}

		public function getIterator() {
			$this->use();
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				yield new File((string)$splFileInfo);
			}
		}

		public function __toString() {
			return $this->directory;
		}

		protected function prepare() {
			$this->directory = FileUtils::realpath($this->directory);
		}
	}
