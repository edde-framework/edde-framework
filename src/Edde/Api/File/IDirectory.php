<?php
	declare(strict_types = 1);

	namespace Edde\Api\File;

	use Edde\Api\Resource\IResource;
	use IteratorAggregate;

	interface IDirectory extends IteratorAggregate {
		/**
		 * return string path of this directory (can be non-existent)
		 *
		 * @return string
		 */
		public function getDirectory();

		/**
		 * return iterator over file list in the current directory
		 *
		 * @return string[]
		 */
		public function getFileList();

		/**
		 * create a file with the given name in this directory
		 *
		 * @param string $name
		 * @param mixed $content
		 *
		 * @return IResource
		 */
		public function save($name, $content);

		/**
		 * get contents of a given file (in this directory)
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public function get($file);

		/**
		 * create filename (shortcut for $this->getDirectory.'\\'.$file)
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public function filename($file);

		/**
		 * return a File object
		 *
		 * @param string $file
		 *
		 * @return IFile
		 */
		public function file(string $file): IFile;

		/**
		 * create all directories until the current one
		 *
		 * @return $this
		 */
		public function create();

		/**
		 * recreate directory in place effectively clean all it's contents
		 *
		 * @return $this
		 */
		public function purge();

		/**
		 * physically remove the directory
		 *
		 * @return $this
		 */
		public function delete();

		/**
		 * @return bool
		 */
		public function exists();

		/**
		 * return directory based on a current path
		 *
		 * @param string $directory
		 *
		 * @return IDirectory
		 */
		public function directory($directory);

		/**
		 * @return IResource[]
		 */
		public function getIterator();
	}
