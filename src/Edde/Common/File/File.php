<?php
	declare(strict_types=1);

	namespace Edde\Common\File;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IFile;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Strings\StringUtils;

	/**
	 * File class; this is just file. Simple goold old classic file. Really.
	 */
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
		protected $autoClose = true;
		/**
		 * @var resource
		 */
		protected $handle;

		/**
		 * @param string|IUrl $file
		 * @param string|null $base
		 *
		 * @throws FileException
		 */
		public function __construct($file, $base = null) {
			parent::__construct($file instanceof IUrl ? $file : FileUtils::url($file), $base);
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			if ($this->name === null) {
				$this->name = $this->url->getResourceName();
			}
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function getDirectory(): IDirectory {
			if ($this->directory === null) {
				$this->directory = new Directory(dirname($this->getPath()));
			}
			return $this->directory;
		}

		/**
		 * @inheritdoc
		 */
		public function getPath(): string {
			return $this->url->getPath();
		}

		/**
		 * @inheritdoc
		 */
		public function getExtension() {
			return $this->url->getExtension();
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function open(string $mode, bool $exclusive = false): IFile {
			if ($this->isOpen()) {
				if ($exclusive === false) {
					return $this;
				}
				throw new FileException(sprintf('Current file [%s] is already opened.', $this->url));
			}
			if (($this->handle = @fopen($path = $this->url->getPath(), $mode)) === false) {
				throw new FileException(sprintf('Cannot open file [%s (%s)].', $path, $mode));
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function openForRead(bool $exclusive = false): IFile {
			$this->open('r+', $exclusive);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function openForWrite(bool $exclusive = false): IFile {
			FileUtils::createDir(dirname($this->url->getPath()));
			$this->open('w+', $exclusive);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function openForAppend(bool $exclusive = false): IFile {
			$this->open('a', $exclusive);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isOpen(): bool {
			return $this->handle !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function enableWriteCache($count = 8): IFile {
			$this->writeCache = $count;
			$this->writeCacheIndex = 0;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function delete(): IFile {
			if ($this->isOpen()) {
				$this->close();
			}
			FileUtils::delete($this->url->getPath());
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function close(): IFile {
			$writeCache = $this->writeCache;
			$this->writeCacheIndex = 2;
			$this->writeCache = 1;
			$this->write('');
			$this->writeCache = $writeCache;
			fflush($handle = $this->getHandle());
			fclose($handle);
			$this->handle = null;
			/** reset write cache by current value */
			$this->enableWriteCache($writeCache);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function write($write, int $length = null): IFile {
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
			if (($count = $length ? fwrite($this->getHandle(), $write, $length) : fwrite($this->getHandle(), $write)) !== ($length = strlen($write))) {
				throw new FileException(sprintf('Failed to write into file [%s]: expected %d bytes, %d has been written.', $this->url->getPath(), $length, $count));
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function getHandle() {
			if ($this->isOpen() === false) {
				throw new FileException(sprintf('Current file [%s] is not opened or has been already closed.', $this->url->getPath()));
			}
			return $this->handle;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function save(string $content): IFile {
			if ($this->isOpen()) {
				throw new FileException(sprintf('Cannot write (save) content to aready opened file [%s].', $this->getPath()));
			}
			file_put_contents($this->url->getPath(), $content);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function rename(string $rename): IFile {
			FileUtils::rename($this->url->getPath(), $rename);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function rewind(): IFile {
			rewind($this->getHandle());
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function read(int $length = null) {
			if (($line = ($length ? fgets($this->getHandle(), $length) : fgets($this->getHandle()))) === false && $this->isAutoClose()) {
				$this->close();
			}
			return $line;
		}

		/**
		 * @inheritdoc
		 */
		public function isAutoClose(): bool {
			return $this->autoClose;
		}

		/**
		 * @inheritdoc
		 */
		public function setAutoClose(bool $autoClose = true): IFile {
			$this->autoClose = $autoClose;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getSize(): float {
			return FileUtils::size($this->getPath());
		}

		/**
		 * @inheritdoc
		 */
		public function lock(bool $exclusive = true): IFile {
			if ($this->isOpen()) {
				throw new FileException(sprintf('File [%s] must be closed to use lock.', $this->getPath()));
			}
			$exclusive ? $this->openForWrite() : $this->openForRead();
			if (flock($this->getHandle(), $exclusive ? LOCK_EX : LOCK_SH) === false) {
				throw new FileException(sprintf('Cannot execute lock on file [%s].', $this->getPath()));
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(): IFile {
			fflush($handle = $this->getHandle());
			flock($handle, LOCK_UN);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function match(string $match, bool $filename = true) {
			return StringUtils::match($filename ? $this->getName() : $this->url->getAbsoluteUrl(), $match);
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
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

		public function __toString() {
			return $this->getPath();
		}
	}
