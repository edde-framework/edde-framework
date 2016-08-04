<?php
	declare(strict_types = 1);

	namespace Edde\Api\Web;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;

	interface IStyleSheetCompiler {
		/**
		 * compile the given list into single resource
		 *
		 * @param IResourceList $resourceList
		 *
		 * @return IResource
		 */
		public function compile(IResourceList $resourceList);
	}
