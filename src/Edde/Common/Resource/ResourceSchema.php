<?php
	namespace Edde\Common\Resource;

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
			// prepare properties
		}
	}
