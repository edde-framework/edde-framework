<?php
	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;

	interface IValueSet {
		/**
		 * @return ISchema
		 */
		public function getSchema();

		/**
		 * @return IValue[]
		 */
		public function getValueList();

		/**
		 * add the given value to this property set
		 *
		 * @param IValue $value
		 * @param bool $force
		 *
		 * @return $this
		 */
		public function addValue(IValue $value, $force = false);

		/**
		 * has this property set property with the given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasValue($name);

		/**
		 * return value of the given name
		 *
		 * @param string $name
		 *
		 * @return IValue
		 */
		public function getValue($name);

		/**
		 * set value of the given property; if does not exists exception is thrown
		 *
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return $this
		 *
		 * @throws CrateException
		 */
		public function set($name, $value);

		/**
		 * put array of values inside this property set; if strict is true, any unknown property will throw exception
		 *
		 * @param array $push
		 * @param bool $strict
		 *
		 * @return $this
		 */
		public function push(array $push, $strict = true);

		/**
		 * return value of the given property; if property does not exist, exception is thrown
		 *
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function get($name, $default = null);

		/**
		 * has been any value in the property list changed?
		 *
		 * @return bool
		 */
		public function isDirty();

		/**
		 * return array of dirty values
		 *
		 * @return IValue[]
		 */
		public function getDirtyList();
	}