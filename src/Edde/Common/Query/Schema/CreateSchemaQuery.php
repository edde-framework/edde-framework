<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Schema;

	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class CreateSchemaQuery extends AbstractQuery implements IConfigurable {
		use ConfigurableTrait;
		/**
		 * @var ISchema
		 */
		protected $schema;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		protected function handleInit() {
			$this->node = new Node('create-schema-query', $this->schema->getSchemaName());
			foreach ($this->schema->getPropertyList() as $schemaProperty) {
				$this->node->addNode($propertyNode = new Node($schemaProperty->getName()));
				$propertyNode->setAttributeList([
					'type' => $schemaProperty->getType(),
					'required' => $schemaProperty->isRequired(),
					'identifier' => $schemaProperty->isIdentifier(),
					'unique' => $schemaProperty->isUnique(),
				]);
			}
		}
	}
