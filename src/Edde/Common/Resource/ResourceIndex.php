<?php
	namespace Edde\Common\Resource;

	use ArrayIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\ResourceException;
	use Edde\Common\AbstractObject;

	class ResourceIndex extends AbstractObject implements IResourceIndex {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IResource[]
		 */
		protected $resourceList = [];

		/**
		 * @param string $name
		 */
		public function __construct($name = null) {
			$this->name = $name ?: static::class;
		}

		public function getIterator() {
			return new ArrayIterator($this->resourceList);
		}

		public function getName() {
			return $this->name;
		}

		public function addResource(IResource $resource, $name = null) {
			$this->resourceList[$name ?: (string)$resource->getUrl()] = $resource;
			return $this;
		}

		public function getResource($name) {
			if ($this->hasResource($name) === false) {
				throw new ResourceException(sprintf('Requested unknown resource [%s] in index [%s].', $name, $this->name));
			}
			return $this->resourceList[$name];
		}

		public function hasResource($name) {
			return isset($this->resourceList[$name]);
		}
	}
