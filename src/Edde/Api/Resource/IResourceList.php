<?php
	namespace Edde\Api\Resource;

	use Iterator;
	use IteratorAggregate;

	interface IResourceList extends IteratorAggregate {
		/**
		 * add resource to the resource list
		 *
		 * @param IResource $resource
		 *
		 * @return $this
		 */
		public function addResource(IResource $resource);

		/**
		 * return hash (name) based on the resources (for example based on a urls)
		 *
		 * @return string
		 */
		public function getResourceName();

		/**
		 * return iterator over this resource list
		 *
		 * @return IResource[]|Iterator
		 */
		public function getResourceList();
	}
