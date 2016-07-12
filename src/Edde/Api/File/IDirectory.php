<?php
	namespace Edde\Api\File;

	interface IDirectory {
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
		 * @return $this
		 */
		public function file($name, $content);

		/**
		 * create all directories until the current one
		 *
		 * @return $this
		 */
		public function make();

		/**
		 * recreate directory in place effectively clean all it's contents
		 *
		 * @return $this
		 */
		public function purge();
	}
