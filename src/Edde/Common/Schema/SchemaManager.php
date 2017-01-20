<?php
	declare(strict_types=1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\LazySchemaFactoryTrait;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Object;

	class SchemaManager extends Object implements ISchemaManager {
		use LazySchemaFactoryTrait;
		use ConfigurableTrait;
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

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			$this->schemaFactory->setup();
			foreach ($this->schemaFactory->create() as $schema) {
				$this->addSchema($schema);
			}
		}
	}
