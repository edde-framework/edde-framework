<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IValue;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Usable\AbstractUsable;

	class Crate extends AbstractUsable implements ICrate {
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
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		public function getSchema() {
			return $this->schema;
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

		protected function prepare() {
			foreach ($this->schema->getPropertyList() as $property) {
				$this->addValue(new Value($property));
			}
		}

		public function addValue(IValue $value, $force = false) {
			$property = $value->getProperty();
			if (isset($this->valueList[$propertyName = $property->getName()]) && $force === false) {
				throw new CrateException(sprintf('Value [%s] is already present in value set [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->valueList[$propertyName] = $value;
			return $this;
		}
	}
