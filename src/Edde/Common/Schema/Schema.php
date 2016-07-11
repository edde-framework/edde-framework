<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\IProperty;
	use Edde\Api\Schema\ISchema;
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
		 * @var IProperty[]
		 */
		protected $propertyList = [];

		/**
		 * @param string $name
		 * @param string $namespace
		 */
		public function __construct($name, $namespace = null) {
			$this->name = $name;
			$this->namespace = $namespace;
		}

		public function getName() {
			return $this->name;
		}

		public function getNamespace() {
			return $this->namespace;
		}

		public function getPropertyList() {
			$this->usse();
			return $this->propertyList;
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

		/**
		 * @param IProperty[] $propertyList
		 *
		 * @return $this
		 *
		 * @throws SchemaException
		 */
		public function addPropertyList(array $propertyList) {
			foreach ($propertyList as $property) {
				$this->addProperty($property);
			}
			return $this;
		}

		/**
		 * @param IProperty $property
		 * @param bool $force
		 *
		 * @return $this
		 *
		 * @throws SchemaException
		 */
		public function addProperty(IProperty $property, $force = false) {
			$propertyName = $property->getName();
			if ($property->getSchema() !== $this) {
				throw new SchemaException(sprintf('Cannot add foreign property [%s] to schema [%s].', $propertyName, $this->getSchemaName()));
			}
			if ($force === false && isset($this->propertyList[$propertyName])) {
				throw new SchemaException(sprintf('Property with name [%s] already exists in schema [%s].', $propertyName, $this->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $property;
			return $this;
		}

		protected function prepare() {
		}
	}
