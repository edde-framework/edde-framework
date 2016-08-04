<?php
	declare(strict_types = 1);

	namespace Edde\Api\Collection;

	use IteratorAggregate;

	/**
	 * Simple list interface for array type checking (for a little bit more complex types than arrays).
	 */
	interface IList extends IteratorAggregate {
		/**
		 * @param string $name
		 * @param string $value
		 *
		 * @return $this
		 */
		public function set($name, $value);

		/**
		 * return true if the given name is set (present) even with null value
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has($name);

		/**
		 * @param string $name
		 * @param string|callable|null $default
		 *
		 * @return string
		 */
		public function get($name, $default = null);

		/**
		 * @return array
		 */
		public function getList();

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function remove($name);
	}
