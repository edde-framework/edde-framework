<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\SchemaException;
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
			if (isset($this->schemaList[$schema]) === false) {
				throw new SchemaException(sprintf('Requested unknown schema [%s].', $schema));
			}
			return $this->schemaList[$schema];
		}

		public function getSchemaList() {
			$this->usse();
			return $this->schemaList;
		}

		protected function prepare() {
		}
	}
