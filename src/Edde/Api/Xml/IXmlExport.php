<?php
	declare(strict_types=1);

	namespace Edde\Api\Xml;

	use Edde\Api\File\IFile;

	interface IXmlExport {
		/**
		 * export the given node to the xml file
		 *
		 * @param \Iterator $iterator
		 * @param IFile     $file
		 *
		 * @return IFile
		 */
		public function export(\Iterator $iterator, IFile $file): IFile;
	}
