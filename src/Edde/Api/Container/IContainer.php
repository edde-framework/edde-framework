<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Cache\ICache;

	/**
	 * Implementation of Dependency Injection Container.
	 */
	interface IContainer {
		/**
		 * if container is using cache, it must be configurable; do not use this as cache solution!
		 *
		 * @return ICache
		 */
		public function getCache(): ICache;

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
		 * register a new config handler for the given dependency
		 *
		 * @param string         $name
		 * @param IConfigHandler $configHandler
		 *
		 * @return IContainer
		 */
		public function registerConfigHandler(string $name, IConfigHandler $configHandler): IContainer;

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
		 * @param array  $parameterList
		 * @param string $source who has requested this dependency
		 *
		 * @return mixed
		 */
		public function create(string $name, array $parameterList = [], string $source = null);

		/**
		 * execute given callback with autowired dependencies
		 *
		 * @param callable $callable
		 * @param array    $parameterList
		 * @param string   $source
		 *
		 * @return mixed
		 */
		public function call(callable $callable, array $parameterList = [], string $source = null);

		/**
		 * general method for dependency creation (so call and create should call this one)
		 *
		 * @param IFactory    $factory
		 * @param array       $parameterList
		 * @param string|null $name
		 * @param string      $source
		 *
		 * @return mixed
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null, string $source = null);

		/**
		 * try to autowire dependencies to $instance
		 *
		 * @param mixed $instance
		 * @param bool  $force if true, dependencies will be autowired regardless of lazy injects
		 *
		 * @return mixed
		 */
		public function autowire($instance, bool $force = false);
	}
