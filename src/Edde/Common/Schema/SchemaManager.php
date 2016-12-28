<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\LazySchemaFactoryTrait;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\AbstractObject;

	class SchemaManager extends AbstractObject implements ISchemaManager {
		use LazySchemaFactoryTrait;
		/**
		 * @var ISchema[]
		 */
		protected $schemaList = [];

		/**
		 * @inheritdoc
		 */
		public function addSchema(ISchema $schema): ISchemaManager {
			$this->schemaList[$schema->getSchemaName()] = $schema;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasSchema(string $schema): bool {
			return isset($this->schemaList[$schema]);
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(string $schema): ISchema {
			if (isset($this->schemaList[$schema]) === false) {
				throw new SchemaException(sprintf('Requested unknown schema [%s].', $schema));
			}
			return $this->schemaList[$schema];
		}

		/**
		 * @inheritdoc
		 */
		public function getSchemaList(): array {
			return $this->schemaList;
		}

		protected function prepare() {
			parent::prepare();
			foreach ($this->schemaFactory->create() as $schema) {
				$this->addSchema($schema);
			}
		}
	}
