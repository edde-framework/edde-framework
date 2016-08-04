<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\SchemaFactoryException;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Usable\AbstractUsable;

	class SchemaFactory extends AbstractUsable implements ISchemaFactory {
		/**
		 * @var INode[]
		 */
		protected $schemaNodeList = [];
		/**
		 * @var INodeQuery
		 */
		protected $propertyListNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $collectionNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $linkNodeQuery;

		public function addSchemaNode(INode $node) {
			$this->schemaNodeList[$this->getSchemaName($node)] = $node;
			return $this;
		}

		protected function getSchemaName(INode $schemaNode) {
			return (($namespace = $schemaNode->getAttribute('namespace')) ? ($namespace . '\\') : null) . $schemaNode->getName();
		}

		public function create() {
			$this->usse();
			/** @var $schemaList ISchema[] */
			$schemaList = [];
			foreach ($this->schemaNodeList as $schemaNode) {
				$schema = $this->createSchema($schemaNode);
				$schemaList[$schema->getSchemaName()] = $schema;
			}
			foreach ($this->schemaNodeList as $schemaNode) {
				foreach ($this->collectionNodeQuery->filter($schemaNode) as $collectionNode) {
					if (isset($schemaList[$schemaName = $collectionNode->getAttribute('schema')]) === false) {
						throw new SchemaFactoryException(sprintf('Cannot use collection to an unknown schema [%s].', $schemaName));
					}
					$sourceSchema = $schemaList[$this->getSchemaName($schemaNode)];
					$targetSchema = $schemaList[$schemaName];
					$sourceSchema->collection($collectionNode->getName(), $sourceSchema->getProperty($collectionNode->getValue()), $targetSchema->getProperty($collectionNode->getAttribute('property')));
				}
				foreach ($this->linkNodeQuery->filter($schemaNode) as $linkNode) {
					if (isset($schemaList[$schemaName = $linkNode->getAttribute('schema')]) === false) {
						throw new SchemaFactoryException(sprintf('Cannot use link to an unknown schema [%s].', $schemaName));
					}
					$sourceSchema = $schemaList[$this->getSchemaName($schemaNode)];
					$targetSchema = $schemaList[$schemaName];
					$sourceSchema->link($linkNode->getName(), $sourceSchema->getProperty($linkNode->getValue($linkNode->getName())), $targetSchema->getProperty($linkNode->getAttribute('property')));
				}
			}
			return $schemaList;
		}

		protected function createSchema(INode $schemaNode) {
			$schema = new Schema($schemaNode->getName(), $schemaNode->getAttribute('namespace'));
			foreach ($this->propertyListNodeQuery->filter($schemaNode) as $propertyNode) {
				$schema->addProperty(new SchemaProperty($schema, $propertyNode->getName(), filter_var($propertyNode->getAttribute('required'), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('unique'), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('identifier'), FILTER_VALIDATE_BOOLEAN)));
			}
			return $schema;
		}

		protected function prepare() {
			$this->propertyListNodeQuery = new NodeQuery('/*/property-list/*');
			$this->collectionNodeQuery = new NodeQuery('/*/collection/*');
			$this->linkNodeQuery = new NodeQuery('/*/link/*');
		}
	}
