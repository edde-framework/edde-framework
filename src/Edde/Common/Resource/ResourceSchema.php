<?php
	namespace Edde\Common\Resource;

	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Usable\UsableTrait;

	class ResourceSchema extends Schema {
		use UsableTrait;

		public function __construct() {
			parent::__construct('Resource', __NAMESPACE__);
		}

		public function getName() {
			return 'Resource';
		}

		public function getNamespace() {
			return __NAMESPACE__;
		}

		public function getSchemaName() {
			return Resource::class;
		}

		protected function prepare() {
			$this->addPropertyList([
				new Property($this, 'guid', 'string', true, true, true, null),
				new Property($this, 'name', 'string', true, true, false, null),
				new Property($this, 'url', 'string', true, true, false, null),
			]);
		}
	}
