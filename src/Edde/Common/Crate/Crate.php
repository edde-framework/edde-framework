<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Usable\AbstractUsable;

	class Crate extends AbstractUsable implements ICrate {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IProperty[]
		 */
		protected $propertyList = [];
		/**
		 * @var IProperty[]
		 */
		protected $identifierList;
		/**
		 * @var ICollection[]
		 */
		protected $collectionList = [];
		/**
		 * @var string[]
		 */
		protected $propertyNameList = [];

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function getSchema() {
			if ($this->schema === null) {
				throw new CrateException(sprintf('Cannot get schema from anonymous crate [%s].', static::class));
			}
			return $this->schema;
		}

		public function setSchema(ISchema $schema) {
			if ($this->isUsed()) {
				throw new CrateException(sprintf('Cannot set schema [%s] to already prepared crate [%s].', $schema->getSchemaName(), static::class));
			}
			$this->schema = $schema;
			return $this;
		}

		public function getPropertyList() {
			$this->usse();
			return $this->propertyList;
		}

		public function getIdentifierList() {
			$this->usse();
			if ($this->identifierList === null) {
				$this->identifierList = [];
				foreach ($this->propertyList as $value) {
					$schemaProperty = $value->getSchemaProperty();
					if ($schemaProperty->isIdentifier()) {
						$this->identifierList[] = $value;
					}
				}
			}
			return $this->identifierList;
		}

		public function put(array $put, $strict = true) {
			$this->usse();
			if ($strict && ($diff = array_diff(array_keys($put), $this->propertyNameList)) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the value set [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($put as $property => $value) {
				if (isset($this->propertyList[$property]) === false) {
					continue;
				}
				$this->set($property, $value);
			}
			return $this;
		}

		public function set($name, $value) {
			$this->usse();
			$this->getValue($name)
				->set($value);
			return $this;
		}

		public function getValue($name) {
			$this->usse();
			if ($this->hasValue($name) === false) {
				throw new CrateException(sprintf('Unknown value [%s] in value set [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->propertyList[$name];
		}

		public function hasValue($name) {
			$this->usse();
			return isset($this->propertyList[$name]);
		}

		public function push(array $push, $strict = true) {
			$this->usse();
			if ($strict && ($diff = array_diff(array_keys($push), $this->propertyNameList)) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the value set [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				if ($this->schema->hasCollection($property)) {
					$collection = $this->collection($property);
					/** @var $value array */
					foreach ($value as $collectionValue) {
						if (is_array($collectionValue) === false) {
							throw new CrateException(sprintf('Cannot push source value into the crate [%s]; value [%s] is not an array (collection).', $this->schema->getSchemaName(), $property));
						}
						$crate = $collection->createCrate();
						$crate->push($collectionValue);
						$collection->addCrate($crate);
					}
					continue;
				}
				if (isset($this->propertyList[$property]) === false) {
					continue;
				}
				$this->getValue($property)
					->push($value);
			}
			return $this;
		}

		public function collection($name) {
			if ($this->schema->hasCollection($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no collection [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			if (isset($this->collectionList[$name]) === false) {
				$collection = $this->schema->getCollection($name);
				$this->collectionList[$name] = $this->container->create(Collection::class, $collection->getTarget()
					->getSchema());
			}
			return $this->collectionList[$name];
		}

		public function get($name, $default = null) {
			$this->usse();
			return $this->getValue($name)
				->get($default);
		}

		public function getDirtyList() {
			$this->usse();
			if ($this->isDirty() === false) {
				return [];
			}
			$valueList = [];
			foreach ($this->propertyList as $value) {
				if ($value->isDirty() === false) {
					continue;
				}
				$property = $value->getSchemaProperty();
				$valueList[$property->getName()] = $value;
			}
			return $valueList;
		}

		public function isDirty() {
			$this->usse();
			foreach ($this->propertyList as $value) {
				if ($value->isDirty()) {
					return true;
				}
			}
			return false;
		}

		public function __clone() {
			if ($this->isUsed()) {
				throw new CrateException(sprintf('Cannot clone used crate [%s].', $this->schema->getSchemaName()));
			}
		}

		protected function prepare() {
			if ($this->schema === null) {
				$this->schema = new Schema('anonymous-crate');
			}
			foreach ($this->schema->getPropertyList() as $property) {
				$this->addValue(new Property($property));
			}
			$this->propertyNameList = array_merge(array_keys($this->propertyList), array_keys($this->schema->getLinkList()), array_keys($this->schema->getCollectionList()));
		}

		public function addValue(IProperty $value, $force = false) {
			$this->usse();
			$property = $value->getSchemaProperty();
			if (isset($this->propertyList[$propertyName = $property->getName()]) && $force === false) {
				throw new CrateException(sprintf('Property [%s] is already present in value set [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $value;
			return $this;
		}
	}
