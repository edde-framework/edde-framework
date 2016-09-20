<?php
	declare(strict_types = 1);

	namespace Edde\Api\File;

	use Edde\Api\Usable\IUsable;

	interface IDirectoryIterator extends \IteratorAggregate, IUsable {
		/**
		 * add directory to iterator; only before use
		 *
		 * @param IDirectory $directory
		 *
		 * @return IDirectoryIterator
		 */
		public function addDirectory(IDirectory $directory): IDirectoryIterator;

		/**
		 * @return IFile[]
		 */
		public function getIterator();
	}
