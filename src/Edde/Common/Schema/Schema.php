<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaLink;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Usable\AbstractUsable;

	class Schema extends AbstractUsable implements ISchema {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var string
		 */
		protected $schemaName;
		/**
		 * @var ISchemaProperty[]
		 */
		protected $propertyList = [];
		/**
		 * @var ISchemaLink[]
		 */
		protected $linkList = [];
		/**
		 * @var ISchemaCollection[]
		 */
		protected $collectionList = [];

		/**
		 * @param string $name
		 * @param string $namespace
		 */
		public function __construct($name, $namespace = null) {
			$this->name = $name;
			$this->namespace = $namespace;
			if ($namespace === null) {
				$nameList = explode('\\', $name);
				$this->name = end($nameList);
				array_pop($nameList);
				if (empty($nameList) === false) {
					$this->namespace = implode('\\', $nameList);
				}
			}
		}

		public function getName() {
			return $this->name;
		}

		public function getNamespace() {
			return $this->namespace;
		}

		public function getProperty($name) {
			$this->usse();
			if ($this->hasProperty($name) === false) {
				throw new SchemaException(sprintf('Requested unknown property [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->propertyList[$name];
		}

		public function hasProperty($name) {
			$this->usse();
			return isset($this->propertyList[$name]);
		}

		public function getSchemaName() {
			if ($this->schemaName === null) {
				$this->schemaName = (($namespace = $this->namespace) !== null ? $namespace . '\\' : null) . $this->name;
			}
			return $this->schemaName;
		}

		public function getPropertyList() {
			$this->usse();
			return $this->propertyList;
		}

		public function addPropertyList(array $schemaPropertyList) {
			foreach ($schemaPropertyList as $schemaProperty) {
				$this->addProperty($schemaProperty);
			}
			return $this;
		}

		public function addProperty(ISchemaProperty $schemaProperty, $force = false) {
			$propertyName = $schemaProperty->getName();
			if ($schemaProperty->getSchema() !== $this) {
				throw new SchemaException(sprintf('Cannot add foreign property [%s] to schema [%s].', $propertyName, $this->getSchemaName()));
			}
			if ($force === false && isset($this->propertyList[$propertyName])) {
				throw new SchemaException(sprintf('Property with name [%s] already exists in schema [%s].', $propertyName, $this->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $schemaProperty;
			return $this;
		}

		public function hasLink($name) {
			return isset($this->linkList[$name]);
		}

		public function getLink($name) {
			if (isset($this->linkList[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown link [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->linkList[$name];
		}

		public function getLinkList() {
			return $this->linkList;
		}

		public function collection($name, ISchemaProperty $source, ISchemaProperty $target, $force = false) {
			if (isset($this->collectionList[$name]) && $force === false) {
				throw new SchemaException(sprintf('Schema [%s] already has collection named [%s].', $this->getSchemaName(), $name));
			}
			$this->collectionList[$name] = new SchemaCollection($name, $source, $target);
			return $this;
		}

		public function hasCollection($name) {
			return isset($this->collectionList[$name]);
		}

		public function getCollection($name) {
			if (isset($this->collectionList[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown collection [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->collectionList[$name];
		}

		public function getCollectionList() {
			return $this->collectionList;
		}

		public function linkTo($link, $collection, ISchemaProperty $source, ISchemaProperty $target) {
			if ($source->getSchema() !== $this) {
				throw new SchemaException(sprintf('Source property [%s] is not part of the current schema [%s].', $source->getPropertyName(), $this->getSchemaName()));
			}
			$this->link($link, $source, $target);
			$target->getSchema()
				->collection($collection, $target, $source);
			return $this;
		}

		public function link($name, ISchemaProperty $source, ISchemaProperty $target, $force = false) {
			if (isset($this->linkList[$name]) && $force === false) {
				throw new SchemaException(sprintf('Schema [%s] already contains link named [%s].', $this->getSchemaName(), $name));
			}
			$this->linkList[$name] = new SchemaLink($name, $source, $target);
			return $this;
		}

		protected function prepare() {
		}
	}
