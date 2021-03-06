<?php
	declare(strict_types=1);

	namespace Edde\Common\File;

	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\FileException;
	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IFile;
	use Edde\Common\Object;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;
	use Symfony\Component\Finder\SplFileInfo;

	/**
	 * Representation of directory on the filesystem.
	 */
	class Directory extends Object implements IDirectory {
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

		/**
		 * @inheritdoc
		 */
		public function getFileList() {
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				yield $path;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getDirectoryList() {
			/** @var $path SplFileInfo */
			foreach (new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				if ($path->isDir()) {
					yield new self((string)$path);
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function normalize(): IDirectory {
			$this->directory = FileUtils::normalize($this->directory);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function realpath(): IDirectory {
			$this->normalize();
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function save(string $file, string $content): IFile {
			file_put_contents($file = $this->filename($file), $content);
			return new File($file);
		}

		/**
		 * @inheritdoc
		 */
		public function filename(string $file): string {
			return FileUtils::normalize($this->getDirectory() . '/' . $file);
		}

		/**
		 * @inheritdoc
		 */
		public function getDirectory(): string {
			return $this->directory;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return basename($this->directory);
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function get(string $file): string {
			return file_get_contents(FileUtils::realpath($this->filename($file)));
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function file(string $file): IFile {
			return new File($this->filename($file));
		}

		/**
		 * @inheritdoc
		 * @throws DirectoryException
		 * @throws FileException
		 */
		public function create(): IDirectory {
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			/** @noinspection NotOptimalIfConditionsInspection */
			if (is_dir($this->directory) === false && @mkdir($this->directory, 0777, true) && is_dir($this->directory) === false) {
				throw new DirectoryException(sprintf('Cannot create directory [%s].', $this->directory));
			}
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function purge(): IDirectory {
			FileUtils::recreate($this->directory);
			$this->directory = FileUtils::realpath($this->directory);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function delete(): IDirectory {
			try {

				FileUtils::delete($this->directory);
			} catch (RealPathException $exception) {
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function exists(): bool {
			return is_dir($this->directory);
		}

		/**
		 * @inheritdoc
		 */
		public function directory(string $directory, string $class = null): IDirectory {
			$class = $class ?: Directory::class;
			return new $class($this->directory . '/' . $directory);
		}

		/**
		 * @inheritdoc
		 */
		public function parent(): IDirectory {
			return new Directory($this->getDirectory() . '/..');
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function files() {
			return $this->getIterator();
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function getIterator() {
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				yield new File((string)$splFileInfo);
			}
		}

		public function __toString() {
			return $this->getDirectory();
		}
	}
