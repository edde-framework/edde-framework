<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * Implementation of Dependency Injection Container.
	 */
	interface IContainer {
		/**
		 * @param IFactory $factory
		 *
		 * @return IContainer
		 */
		public function registerFactory(IFactory $factory): IContainer;

		/**
		 * shorthand for cache registration
		 *
		 * @param array $factoryList
		 *
		 * @return IContainer
		 */
		public function registerFactoryList(array $factoryList): IContainer;

		/**
		 * register list of config handlers bound to the given factories (key is factory name, value is config handler)
		 *
		 * @param IConfigHandler[] $configHandlerList
		 *
		 * @return IContainer
		 */
		public function registerConfigHandlerList(array $configHandlerList): IContainer;

		/**
		 * get factory which is able to create the given dependency
		 *
		 * @param mixed $dependency
		 *
		 * @return IFactory
		 */
		public function getFactory(string $dependency): IFactory;

		/**
		 * create the dependency by it's identifier (name)
		 *
		 * @param string $name
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function create(string $name, ...$parameterList);

		/**
		 * execute given callback with autowired dependencies
		 *
		 * @param callable $callable
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function call(callable $callable, ...$parameterList);

		/**
		 * provides all aditional dependencies for the given instance
		 *
		 * @param mixed $instance
		 * @param IDependency $dependency
		 *
		 * @return mixed return input instance (input is same as output)
		 */
		public function inject($instance, IDependency $dependency = null);
	}
