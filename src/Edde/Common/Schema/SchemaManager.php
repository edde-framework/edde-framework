<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Usable\AbstractUsable;

	class SchemaManager extends AbstractUsable implements ISchemaManager {
		/**
		 * @var ISchema[]
		 */
		protected $schemaList = [];

		public function addSchema(ISchema $schema) {
			$this->schemaList[$schema->getSchemaName()] = $schema;
			return $this;
		}

		public function hasSchema($schema) {
			$this->usse();
			return isset($this->schemaList[$schema]);
		}

		public function getSchema($schema) {
			$this->usse();
			return $this->schemaList[$schema];
		}

		protected function prepare() {
		}
	}
