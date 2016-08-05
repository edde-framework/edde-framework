<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource;

	use Edde\Api\Node\INode;

	interface IResourceHandler {
		/**
		 * return list of mime types supported by this handler
		 *
		 * @return array
		 */
		public function getMimeTypeList(): array;

		/**
		 * handle the given resource; output should be always INode; for example loaded image can have name as file name and value as opened handler
		 *
		 * @param IResource $resource
		 *
		 * @return INode
		 */
		public function handle(IResource $resource): INode;
	}
