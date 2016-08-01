<?php
	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchemaProperty;

	/**
	 * The physical value of the crate.
	 */
	interface IValue {
		/**
		 * return value's properties (property definition)
		 *
		 * @return ISchemaProperty
		 */
		public function getSchemaProperty();

		/**
		 * set value to this property; the original value is preserved
		 *
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function set($value);

		/**
		 * push value; the original value is set to this value and current value is nulled; value is NOT dirty after this
		 *
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function push($value);

		/**
		 * retrieve current value or default; default value should be updated to the property
		 *
		 * @param mixed|null $default can be callback
		 *
		 * @return mixed
		 */
		public function get($default = null);

		/**
		 * return the original value
		 *
		 * @return mixed
		 */
		public function getValue();

		/**
		 * has been this value changed from the original one?
		 *
		 * @return bool
		 */
		public function isDirty();

		/**
		 * forgot current value
		 *
		 * @return $this
		 */
		public function reset();
	}
