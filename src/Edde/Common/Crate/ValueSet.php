<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\IValue;
	use Edde\Api\Crate\IValueSet;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\AbstractObject;

	/**
	 * ValueSet simplifies work with values over... set.
	 */
	class ValueSet extends AbstractObject implements IValueSet {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IValue[]
		 */
		protected $valueList = [];

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
			return $this->valueList;
		}

		public function push(array $push, $strict = true) {
			if ($strict && ($diff = array_diff(array_keys($push), array_keys($this->valueList))) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the value set [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				if (isset($this->valueList[$property]) === false) {
					continue;
				}
				$this->set($property, $value);
			}
			return $this;
		}

		public function set($name, $value) {
			$this->getValue($name)
				->set($value);
			return $this;
		}

		public function getValue($name) {
			if ($this->hasValue($name) === false) {
				throw new CrateException(sprintf('Unknown value [%s] in value set [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->valueList[$name];
		}

		public function hasValue($name) {
			return isset($this->valueList[$name]);
		}

		public function get($name, $default = null) {
			return $this->getValue($name)
				->get($default);
		}

		public function getDirtyList() {
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
			foreach ($this->valueList as $value) {
				if ($value->isDirty()) {
					return true;
				}
			}
			return false;
		}
	}
