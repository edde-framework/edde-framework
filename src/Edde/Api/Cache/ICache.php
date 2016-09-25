<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	/**
	 * Formal interface for a cache implementation.
	 */
	interface ICache {
		/**
		 * cache method result
		 *
		 * @param string $name
		 * @param callable $callback
		 * @param array ...$parameterList
		 *
		 * @return string
		 */
		public function callback($name, callable $callback, ...$parameterList);

		/**
		 * save given value into the cache
		 *
		 * @param string $id
		 * @param mixed $save must be serializable (neonable, jsonable, serializable, ...)
		 *
		 * @return mixed
		 */
		public function save($id, $save);

		/**
		 * load value be the id - if the value doesn't exists, default is returned
		 *
		 * @param string $id
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function load($id, $default = null);

		/**
		 * manual invalidation of whole cache
		 *
		 * @return $this
		 */
		public function invalidate();
	}
