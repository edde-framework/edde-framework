<?php
	namespace Edde\Api\Resource;

	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorable;

	interface IResourceManager {
		/**
		 * rescan all available resources; this will drop current resource index and trigger scanner
		 *
		 * note: this operation can be really heavy, so use it only when needed
		 *
		 * @return $this
		 */
		public function update();

		/**
		 * return collection of resources by the given query
		 *
		 * @param IResourceQuery $resourceQuery
		 *
		 * @return ICollection
		 */
		public function getResourceCollection(IResourceQuery $resourceQuery);

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

		/**
		 * @return IStorable
		 */
		public function createResourceStorable();
	}
