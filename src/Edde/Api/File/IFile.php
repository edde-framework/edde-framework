<?php
	declare(strict_types=1);

	namespace Edde\Api\File;

	use Edde\Api\Resource\IResource;

	interface IFile extends IResource {
		/**
		 * enable autoclose after file is read
		 *
		 * @param bool|true $autoClose
		 *
		 * @return IFile
		 */
		public function setAutoClose(bool $autoClose = true): IFile;

		/**
		 * @return string
		 */
		public function getPath(): string;

		/**
		 * @return string
		 */
		public function getExtension(): string;

		/**
		 * return directory of this file
		 *
		 * @return IDirectory
		 */
		public function getDirectory(): IDirectory;

		/**
		 * @return bool
		 */
		public function isAutoClose(): bool;

		/**
		 * rename a file
		 *
		 * @param string $rename
		 *
		 * @return IFile
		 */
		public function rename(string $rename): IFile;

		/**
		 * create file handle; if the file is not availble, exceptio nshould be thrown
		 *
		 * @param string $mode
		 *
		 * @return IFile
		 */
		public function open(string $mode): IFile;

		/**
		 * @return bool
		 */
		public function isOpen(): bool;

		/**
		 * return file's resource; if it is not open, exception should be thrown
		 *
		 * @return resource
		 */
		public function getHandle();

		/**
		 * close the current file handle
		 *
		 * @return IFile
		 */
		public function close(): IFile;

		/**
		 * @return IFile
		 */
		public function openForRead(): IFile;

		/**
		 * @return IFile
		 */
		public function openForWrite(): IFile;

		/**
		 * @return IFile
		 */
		public function openForAppend(): IFile;

		/**
		 * read bunch of data
		 *
		 * @param int $length
		 *
		 * @return mixed
		 */
		public function read(int $length = null);

		/**
		 * write bunch of data
		 *
		 * @param mixed $write
		 * @param int   $length
		 *
		 * @return IFile
		 */
		public function write($write, int $length = null): IFile;

		/**
		 * override current file with the given content
		 *
		 * @param string $content
		 *
		 * @return IFile
		 */
		public function save(string $content): IFile;

		/**
		 * enable write cache; write is performed after number of calls or on file close; disabled (=== 0) by default
		 *
		 * @param int $count
		 *
		 * @return IFile
		 */
		public function enableWriteCache($count = 8): IFile;

		/**
		 * @return IFile
		 */
		public function rewind(): IFile;

		/**
		 * @return IFile
		 */
		public function delete(): IFile;

		/**
		 * return a file size
		 *
		 * @return float
		 */
		public function getSize(): float;

		/**
		 * create a file and do an exclusive lock or lock an existing file; if lock cannot be acquired, exception should be thrown
		 *
		 * @param bool $exclusive
		 *
		 * @return IFile
		 */
		public function lock(bool $exclusive = true): IFile;

		/**
		 * unlock the file or throw an exception if file is not locked
		 *
		 * @return IFile
		 */
		public function unlock(): IFile;

		/**
		 * run regexp against file path
		 *
		 * @param string $match
		 * @param bool   $filename
		 *
		 * @return mixed
		 */
		public function match(string $match, bool $filename = true);
	}
