<?php
	namespace Edde\Common\Query\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class CreateSchemaQuery extends AbstractQuery {
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

		protected function prepare() {
			$this->node = new Node('create-schema-query', $this->schema->getSchemaName());
			foreach ($this->schema->getPropertyList() as $property) {
				$this->node->addNode($propertyNode = new Node($property->getName()));
				$propertyNode->setAttributeList([
					'type' => $property->getType(),
					'required' => $property->isRequired(),
					'identifier' => $property->isIdentifier(),
					'unique' => $property->isUnique(),
				]);
			}
		}
	}
