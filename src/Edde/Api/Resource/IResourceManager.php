<?php
	namespace Edde\Api\Resource;

	interface IResourceManager {
		/**
		 * query a ResourceManager by the given ResourceQuery for a Resource; if there is not such resource, exception should be thrown
		 *
		 * @param IResourceQuery $resourceQuery
		 *
		 * @return IResource
		 *
		 * @throws ResourceException
		 */
		public function getResource(IResourceQuery $resourceQuery);
	}
