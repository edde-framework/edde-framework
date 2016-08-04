<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	class ResourceSchema extends Schema {
		public function __construct() {
			parent::__construct(ResourceStorable::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				(new SchemaProperty($this, 'name'))->required(),
				(new SchemaProperty($this, 'url'))->unique()
					->required(),
				new SchemaProperty($this, 'base'),
				new SchemaProperty($this, 'extension'),
				new SchemaProperty($this, 'mime'),
			]);
		}
	}
