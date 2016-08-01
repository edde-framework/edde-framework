<?php
	namespace Edde\Common\Resource;

	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	class ResourceSchema extends Schema {
		public function __construct() {
			parent::__construct(ResourceStorable::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				new SchemaProperty($this, 'guid', 'string', true, true, true, null),
				new SchemaProperty($this, 'name', 'string', true, false, false, null),
				new SchemaProperty($this, 'url', 'string', true, true, false, null),
				new SchemaProperty($this, 'base', 'string', false, false, false, null),
				new SchemaProperty($this, 'extension', 'string', false, false, false, null),
				new SchemaProperty($this, 'mime', 'string', false, false, false, null),
			]);
		}
	}
