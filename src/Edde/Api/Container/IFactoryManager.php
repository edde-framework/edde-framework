<?php
	declare(strict_types = 1);

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
		 * @return IFactoryManager
		 */
		public function registerFactory(string $name, IFactory $factory): IFactoryManager;

		/**
		 * @param IFactory[] $factoryList
		 *
		 * @return IFactoryManager
		 */
		public function registerFactoryList(array $factoryList): IFactoryManager;

		/**
		 * @param callable $callback
		 *
		 * @return IFactoryManager
		 */
		public function registerFactoryFallback(callable $callback): IFactoryManager;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasFactory(string $name): bool;

		/**
		 * @param string $name
		 *
		 * @return IFactory
		 *
		 * @throws FactoryException
		 */
		public function getFactory(string $name): IFactory;
	}
