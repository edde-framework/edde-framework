<?php
	namespace Edde\Api\Container;

	/**
	 * General purpose factory management; used byt an Container and a DependencyFactory.
	 */
	interface IFactoryManager {
		/**
		 * register given factory
		 *
		 * @param string $name
		 * @param IFactory $factory
		 *
		 * @return $this
		 */
		public function registerFactory($name, IFactory $factory);

		/**
		 * @param IFactory[] $factoryList
		 *
		 * @return $this
		 */
		public function registerFactoryList($factoryList);

		/**
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function registerFactoryFallback(callable $callback);

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasFactory($name);

		/**
		 * @param string $name
		 *
		 * @return IFactory
		 *
		 * @throws FactoryException
		 */
		public function getFactory($name);
	}
