<?php
	namespace Edde\Api\Resource;

	use IteratorAggregate;

	/**
	 * Resource index holds set of available IResources; linear data interpretation is intentional; every IResource here should
	 * be in the same level (idea of make from ResourceIndex tree was there and closed).
	 */
	interface IResourceIndex extends IteratorAggregate {
		/**
		 * name of this index; for example it can be name of scanned folder
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * add resource to the index; this is only way how to build index
		 *
		 * @param IResource $resource
		 * @param string|null $name if name is not specified, absolute URL should be used
		 *
		 * @return $this
		 */
		public function addResource(IResource $resource, $name = null);

		/**
		 * is the given name known as resource in this index?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasResource($name);

		/**
		 * request resource; if it is not available (not in index), an exception should be thrown
		 *
		 * @param string $name
		 *
		 * @return IResource
		 */
		public function getResource($name);
	}
