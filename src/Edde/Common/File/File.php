<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IFile;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Resource\Resource;

	class File extends Resource implements IFile {
		/**
		 * @var int
		 */
		protected $writeCache = 0;
		protected $writeCacheData = [];
		protected $writeCacheIndex = 0;
		/**
		 * @var IDirectory
		 */
		protected $directory;
		/**
		 * @var bool
		 */
		private $autoClose = true;
		/**
		 * @var resource
		 */
		private $handle;

		/**
		 * @param string|IUrl $file
		 * @param string|null $base
		 *
		 * @throws FileException
		 */
		public function __construct($file, $base = null) {
			parent::__construct($file instanceof IUrl ? $file : FileUtils::url($file), $base);
		}

		public function getName() {
			if ($this->name === null) {
				$this->name = $this->url->getResourceName();
			}
			return $this->name;
		}

		public function getDirectory(): IDirectory {
			if ($this->directory === null) {
				$this->directory = new Directory(dirname($this->getPath()));
			}
			return $this->directory;
		}

		public function getPath(): string {
			return $this->url->getPath();
		}

		public function getExtension(): string {
			return $this->url->getExtension();
		}

		public function setAutoClose(bool $autoClose = true): IFile {
			$this->autoClose = $autoClose;
			return $this;
		}

		public function openForAppend(): IFile {
			$this->open('a');
			return $this;
		}

		public function open(string $mode): IFile {
			if ($this->isOpen()) {
				throw new FileException(sprintf('Current file [%s] is already opened.', $this->url));
			}
			if (($this->handle = fopen($this->url->getPath(), $mode)) === false) {
				throw new FileException(sprintf('Cannot open file [%s (%s)].', $this->url->getPath(), $mode));
			}
			return $this;
		}

		public function isOpen(): bool {
			return $this->handle !== null;
		}

		public function enableWriteCache($count = 8): IFile {
			$this->writeCache = $count;
			$this->writeCacheIndex = 0;
			return $this;
		}

		public function delete(): IFile {
			if ($this->isOpen()) {
				$this->close();
			}
			FileUtils::delete($this->url->getPath());
			return $this;
		}

		public function close(): IFile {
			$writeCache = $this->writeCache;
			$this->writeCacheIndex = 2;
			$this->writeCache = 1;
			$this->write('');
			$this->writeCache = $writeCache;
			fflush($handle = $this->getHandle());
			fclose($handle);
			$this->handle = null;
			return $this;
		}

		public function write($write): IFile {
			if ($this->isOpen() === false) {
				$this->openForWrite();
			}
			if ($this->writeCache > 0) {
				$this->writeCacheData[] = $write;
				if ($this->writeCacheIndex++ < $this->writeCache) {
					return $this;
				}
				$write = implode('', $this->writeCacheData);
				$this->writeCacheData = [];
				$this->writeCacheIndex = 0;
			}
			$written = fwrite($this->getHandle(), $write);
			if ($written !== ($lengh = strlen($write))) {
				throw new FileException(sprintf('Failed to write into file [%s]: expected %d bytes, %d has been written.', $this->url->getPath(), $lengh, $written));
			}
			return $this;
		}

		public function openForWrite(): IFile {
			FileUtils::createDir(dirname($this->url->getPath()));
			$this->open('w+');
			return $this;
		}

		public function getHandle() {
			if ($this->isOpen() === false) {
				throw new FileException(sprintf('Current file [%s] is not opened or has been already closed.', $this->url->getPath()));
			}
			return $this->handle;
		}

		public function rename(string $rename): IFile {
			FileUtils::rename($this->url->getPath(), $rename);
			return $this;
		}

		public function getIterator() {
			if ($this->isOpen() === false) {
				$this->openForRead();
			}
			$this->rewind();
			$count = 0;
			while ($line = $this->read()) {
				yield $count++ => $line;
			}
		}

		public function openForRead(): IFile {
			$this->open('r+');
			return $this;
		}

		public function rewind(): IFile {
			rewind($this->getHandle());
			return $this;
		}

		public function read() {
			if (($line = fgets($this->getHandle())) === false && $this->isAutoClose()) {
				$this->close();
			}
			return $line;
		}

		public function isAutoClose(): bool {
			return $this->autoClose;
		}
	}
