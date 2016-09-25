<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\AbstractObject;

	/**
	 * Cache class is wrapper around low-level ICacheStorage. It is intended to use new instance per caching scope.
	 */
	class Cache extends AbstractObject implements ICache {
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;
		/**
		 * @var string
		 */
		protected $namespace;

		/**
		 * @param ICacheStorage $cacheStorage
		 * @param string $namespace
		 */
		public function __construct(ICacheStorage $cacheStorage, $namespace = null) {
			$this->cacheStorage = $cacheStorage;
			$this->namespace = $namespace;
		}

		public function callback($name, callable $callback, ...$parameterList) {
			if (($result = $this->load($name)) !== null) {
				return $result;
			}
			return $this->save($name, call_user_func_array($callback, $parameterList));
		}

		public function load($id, $default = null) {
			if (($value = $this->cacheStorage->load($this->cacheId($id))) === null) {
				return is_callable($default) ? call_user_func($default) : $default;
			}
			return $value;
		}

		protected function cacheId($id) {
			return sha1($this->namespace . '/' . $id);
		}

		public function save($id, $source) {
			$this->cacheStorage->save($this->cacheId($id), $source);
			return $source;
		}

		public function invalidate() {
			$this->cacheStorage->invalidate();
			return $this;
		}
	}
