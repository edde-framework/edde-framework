<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\IValue;
	use Edde\Api\Schema\IProperty;
	use Edde\Common\AbstractObject;

	class Value extends AbstractObject implements IValue {
		/**
		 * property definition of this value
		 *
		 * @var IProperty
		 */
		protected $property;
		/**
		 * the original value of this property
		 *
		 * @var mixed
		 */
		protected $value;
		/**
		 * current value of this property
		 *
		 * @var mixed
		 */
		protected $current;
		/**
		 * has been this property changed?
		 *
		 * @var bool
		 */
		protected $dirty;

		/**
		 * @param IProperty $property
		 * @param mixed|null $value
		 */
		public function __construct(IProperty $property, $value = null) {
			$this->property = $property;
			$this->value = $value;
			$this->dirty = false;
		}

		public function getProperty() {
			return $this->property;
		}

		public function push($value) {
			$this->dirty = false;
			$this->current = null;
			$this->value = $value;
			return $this;
		}

		public function get($default = null) {
			if ($this->current === null && $this->value === null) {
				$this->set($value = is_callable($default) ? call_user_func($default) : $default);
				return $value;
			}
			if ($this->dirty) {
				return $this->current;
			}
			return $this->value;
		}

		public function set($value) {
			$this->dirty = false;
			$this->current = null;
			if ($this->value !== $value) {
				$this->dirty = true;
				$this->current = $value;
			}
			return $this;
		}

		public function getValue() {
			return $this->value;
		}

		public function isDirty() {
			return $this->dirty;
		}

		public function reset() {
			$this->dirty = false;
			$this->current = null;
			return $this;
		}
	}
