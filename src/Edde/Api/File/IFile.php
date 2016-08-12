<?php
	declare(strict_types = 1);

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
		 * @return mixed
		 */
		public function read();

		/**
		 * write bunch of data
		 *
		 * @param mixed $write
		 *
		 * @return IFile
		 */
		public function write($write): IFile;

		/**
		 * @return IFile
		 */
		public function rewind(): IFile;

		/**
		 * @return IFile
		 */
		public function delete(): IFile;
	}
