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
		public function __construct(string $directory) {
			$this->directory = $directory;
		}

		public function getFileList() {
			$this->use();
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				yield $path;
			}
		}

		public function save(string $file, string $content): IFile {
			$this->use();
			file_put_contents($file = $this->filename($file), $content);
			return new File($file);
		}

		public function filename(string $file): string {
			return FileUtils::normalize($this->getDirectory() . '/' . $file);
		}

		public function getDirectory(): string {
			$this->use();
			return $this->directory;
		}

		public function get(string $file): string {
			return file_get_contents(FileUtils::realpath($this->filename($file)));
		}

		public function file(string $file): IFile {
			return new File($this->filename($file));
		}

		public function create(): IDirectory {
			if (is_dir($this->directory) === false && @mkdir($this->directory, 0777, true) && is_dir($this->directory) === false) {
				throw new DirectoryException(sprintf('Cannot create directory [%s].', $this->directory));
			}
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		public function purge(): IDirectory {
			FileUtils::recreate($this->directory);
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		public function delete(): IDirectory {
			try {
				$this->use();
				FileUtils::delete($this->directory);
			} catch (RealPathException $exception) {
			}
			return $this;
		}

		public function exists(): bool {
			return is_dir($this->directory);
		}

		public function directory(string $directory, string $class = null): IDirectory {
			$class = $class ?: Directory::class;
			return new $class($this->directory . '/' . $directory);
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
			return $this->getDirectory();
		}

		protected function prepare() {
			$this->directory = FileUtils::realpath($this->directory);
		}
	}
