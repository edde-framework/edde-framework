<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceManager extends AbstractUsable implements IResourceManager {
		public function getResource(IResourceQuery $resourceQuery) {
			$this->usse();
		}

		protected function prepare() {
		}
	}
