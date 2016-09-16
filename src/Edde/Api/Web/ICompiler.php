<?php
	declare(strict_types = 1);

	namespace Edde\Api\Web;

	use Edde\Api\File\IFile;
	use Edde\Api\Resource\IResourceList;

	interface ICompiler extends IResourceList {
		/**
		 * general resource list to resource conversion (compilation)
		 *
		 * @param IResourceList $resourceList
		 *
		 * @return IFile
		 */
		public function compile(IResourceList $resourceList): IFile;
	}
