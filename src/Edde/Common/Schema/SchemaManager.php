<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Usable\AbstractUsable;

	class SchemaManager extends AbstractUsable implements ISchemaManager {
		/**
		 * @var ISchemaFactory
		 */
		protected $schemaFactory;
		/**
		 * @var ISchema[]
		 */
		protected $schemaList = [];

		/**
		 * @param ISchemaFactory $schemaFactory
		 */
		public function lazySchemaFactory(ISchemaFactory $schemaFactory) {
			$this->schemaFactory = $schemaFactory;
		}

		public function hasSchema($schema) {
			$this->use();
			return isset($this->schemaList[$schema]);
		}

		public function getSchema($schema) {
			$this->use();
			if (isset($this->schemaList[$schema]) === false) {
				throw new SchemaException(sprintf('Requested unknown schema [%s].', $schema));
			}
			return $this->schemaList[$schema];
		}

		public function getSchemaList() {
			$this->use();
			return $this->schemaList;
		}

		protected function prepare() {
			foreach ($this->schemaFactory->create() as $schema) {
				$this->addSchema($schema);
			}
		}

		public function addSchema(ISchema $schema) {
			$this->schemaList[$schema->getSchemaName()] = $schema;
			return $this;
		}
	}
