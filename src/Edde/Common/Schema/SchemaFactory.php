<?php
	declare(strict_types=1);

	namespace Edde\Common\Schema;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaProvider;
	use Edde\Api\Schema\SchemaException;
	use Edde\Api\Schema\SchemaFactoryException;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Object;

	class SchemaFactory extends Object implements ISchemaFactory {
		use LazyContainerTrait;
		use ConfigurableTrait;
		/**
		 * @var ISchemaProvider[]
		 */
		protected $schemaProviderList = [];
		/**
		 * @var INodeQuery
		 */
		protected $propertyListNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertyFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertySetterFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertyGetterFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $collectionNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $linkNodeQuery;

		/**
		 * @inheritdoc
		 */
		public function registerSchemaProvider(ISchemaProvider $schemaProvider): ISchemaFactory {
			$this->schemaProviderList[] = $schemaProvider;
			return $this;
		}

		protected function getSchemaName(INode $schemaNode) {
			return (($namespace = $schemaNode->getAttribute('namespace')) ? ($namespace . '\\') : null) . $schemaNode->getName();
		}

		/**
		 * @inheritdoc
		 */
		public function createSchema(INode $node): ISchema {
			$schema = new Schema($node->getName(), $node->getAttribute('namespace'));
			$schema->setMetaList($node->getMetaList());
			$magic = $schema->getMeta('magic', true);
			foreach ($this->propertyListNodeQuery->filter($node) as $propertyNode) {
				$schema->addProperty($property = new Property($schema, $propertyNode->getName(), str_replace('[]', '', $type = $propertyNode->getAttribute('type', 'string')), filter_var($propertyNode->getAttribute('required', true), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('unique'), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('identifier'), FILTER_VALIDATE_BOOLEAN), strpos($type, '[]') !== false));
				if (($generator = $propertyNode->getAttribute('generator')) !== null) {
					$property->setGenerator($this->container->create($generator, [], __METHOD__));
				}
				$type = $property->getType();
				foreach ($this->propertyFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				foreach ($this->propertySetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addSetterFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				foreach ($this->propertyGetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addGetterFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				/** @noinspection DisconnectedForeachInstructionInspection */
				/**
				 * magical things can be turned off
				 */
				if ($magic === false) {
					$type = null;
				}
				/**
				 * support for automagical type conversions
				 */
				switch ($type) {
					case 'bool':
						$property->addFilter(new BoolFilter());
						break;
				}
			}
			return $schema;
		}

		/**
		 * @inheritdoc
		 */
		public function create(): array {
			/** @var $schemaList ISchema[] */
			if (empty($this->schemaProviderList)) {
				throw new SchemaException(sprintf("There are no schema providers or you didn't run [%s::setup()] method.", static::class));
			}
			$schemaList = [];
			foreach ($this->schemaProviderList as $schemaProvider) {
				foreach ($schemaProvider as $node) {
					$schema = $this->createSchema($node);
					$schemaList[$schema->getSchemaName()] = $schema;
				}
			}
			foreach ($this->schemaProviderList as $schemaProvider) {
				foreach ($schemaProvider as $node) {
					$sourceSchema = $schemaList[$this->getSchemaName($node)];
					foreach ($this->collectionNodeQuery->filter($node) as $collectionNode) {
						if (isset($schemaList[$schemaName = $collectionNode->getAttribute('schema')]) === false) {
							throw new SchemaFactoryException(sprintf('Cannot use collection to an unknown schema [%s].', $schemaName));
						}
						$targetSchema = $schemaList[$schemaName];
						$sourceSchema->collection($collectionNode->getName(), $sourceSchema->getProperty($collectionNode->getValue()), $targetSchema->getProperty($collectionNode->getAttribute('property')));
					}
					foreach ($this->linkNodeQuery->filter($node) as $linkNode) {
						if (isset($schemaList[$schemaName = $linkNode->getAttribute('schema')]) === false) {
							throw new SchemaFactoryException(sprintf('Cannot use link to an unknown schema [%s].', $schemaName));
						}
						$targetSchema = $schemaList[$schemaName];
						$sourceSchema->link($linkNode->getName(), $sourceSchema->getProperty($linkNode->getValue($linkNode->getName())), $targetSchema->getProperty($linkNode->getAttribute('property')));
					}
				}
			}
			return $schemaList;
		}

		protected function handleInit() {
			parent::handleInit();
			$this->propertyListNodeQuery = new NodeQuery('/*/property-list/*');
			$this->propertyFilterNodeQuery = new NodeQuery('/*/property-list/*/filter/*');
			$this->propertySetterFilterNodeQuery = new NodeQuery('/*/property-list/*/setter-filter/*');
			$this->propertyGetterFilterNodeQuery = new NodeQuery('/*/property-list/*/getter-filter/*');
			$this->collectionNodeQuery = new NodeQuery('/*/collection/*');
			$this->linkNodeQuery = new NodeQuery('/*/link/*');
		}
	}
