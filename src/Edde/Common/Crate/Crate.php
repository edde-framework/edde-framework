<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IValue;
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
		 * @var IValue[]
		 */
		protected $valueList = [];
		/**
		 * @var IValue[]
		 */
		protected $identifierList;
		/**
		 * @var ICollection[]
		 */
		protected $collectionList = [];

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

		public function getValueList() {
			$this->usse();
			return $this->valueList;
		}

		public function getIdentifierList() {
			$this->usse();
			if ($this->identifierList === null) {
				$this->identifierList = [];
				foreach ($this->valueList as $value) {
					$property = $value->getProperty();
					if ($property->isIdentifier()) {
						$this->identifierList[] = $value;
					}
				}
			}
			return $this->identifierList;
		}

		public function put(array $put, $strict = true) {
			$this->usse();
			if ($strict && ($diff = array_diff(array_keys($put), array_keys($this->valueList))) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the value set [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($put as $property => $value) {
				if (isset($this->valueList[$property]) === false) {
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
			return $this->valueList[$name];
		}

		public function hasValue($name) {
			$this->usse();
			return isset($this->valueList[$name]);
		}

		public function push(array $push, $strict = true) {
			$this->usse();
			if ($strict && ($diff = array_diff(array_keys($push), array_keys($this->valueList))) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the value set [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				if (isset($this->valueList[$property]) === false) {
					continue;
				}
				$this->getValue($property)
					->push($value);
			}
			return $this;
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
			$propertyList = [];
			foreach ($this->valueList as $value) {
				if ($value->isDirty() === false) {
					continue;
				}
				$property = $value->getProperty();
				$propertyList[$property->getName()] = $value;
			}
			return $propertyList;
		}

		public function isDirty() {
			$this->usse();
			foreach ($this->valueList as $value) {
				if ($value->isDirty()) {
					return true;
				}
			}
			return false;
		}

		public function collection($name) {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			if (isset($this->collectionList[$name]) === false) {
				$link = $this->schema->getLink($name);
				$this->collectionList[$name] = $this->container->create(Collection::class, $link->getTarget()
					->getSchema());
			}
			return $this->collectionList[$name];
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
				$this->addValue(new Value($property));
			}
		}

		public function addValue(IValue $value, $force = false) {
			$this->usse();
			$property = $value->getProperty();
			if (isset($this->valueList[$propertyName = $property->getName()]) && $force === false) {
				throw new CrateException(sprintf('Value [%s] is already present in value set [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->valueList[$propertyName] = $value;
			return $this;
		}
	}
