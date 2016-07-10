<?php
	namespace Edde\Common\Collection;

	use ArrayIterator;
	use Edde\Api\Collection\IList;
	use Edde\Common\AbstractObject;

	/**
	 * This list implementation is abstract because it should be not possible to use
	 * untyped lists accross an application.
	 */
	abstract class AbstractList extends AbstractObject implements IList {
		/**
		 * @var string[]
		 */
		protected $list = [];

		public function set($name, $value) {
			$this->list[$name] = $value;
			return $this;
		}

		public function get($name, $default = null) {
			if ($this->has($name) === false) {
				return is_callable($default) ? call_user_func($default) : $default;
			}
			return $this->list[$name];
		}

		public function has($name) {
			return isset($this->list[$name]) || array_key_exists($this->list, $name);
		}

		public function remove($name) {
			unset($this->list[$name]);
			return $this;
		}

		public function getIterator() {
			return new ArrayIterator($this->list);
		}
	}
