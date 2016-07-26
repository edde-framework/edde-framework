<?php
	namespace Edde\Common\Resource;

	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;

	class ResourceSchema extends Schema {
		public function __construct() {
			parent::__construct(ResourceStorable::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				new Property($this, 'guid', 'string', true, true, true, null),
				new Property($this, 'name', 'string', true, false, false, null),
				new Property($this, 'url', 'string', true, true, false, null),
				new Property($this, 'base', 'string', false, false, false, null),
				new Property($this, 'extension', 'string', false, false, false, null),
				new Property($this, 'mime', 'string', false, false, false, null),
			]);
		}
	}
