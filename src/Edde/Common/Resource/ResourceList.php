<?php
	namespace Edde\Common\Resource;

	use ArrayIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Common\AbstractObject;

	class ResourceList extends AbstractObject implements IResourceList {
		protected $resourceList = [];

		public function addResource(IResource $resource) {
			$this->resourceList[(string)$resource->getUrl()] = $resource;
			return $this;
		}

		public function getResourceName() {
			return sha1(implode('', array_keys($this->resourceList)));
		}

		public function getIterator() {
			return $this->getResourceList();
		}

		public function getResourceList() {
			return new ArrayIterator($this->resourceList);
		}

		public function isEmpty() {
			return empty($this->resourceList);
		}
	}
