<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Usable\IUsable;

	/**
	 * Implementation of Dependency Inject Container.
	 */
	interface IContainer extends IUsable {
		/**
		 * quickhand to IFactoryManager
		 *
		 * @param string $name
		 * @param IFactory $factory
		 *
		 * @return IContainer
		 */
		public function registerFactory(string $name, IFactory $factory): IContainer;

		/**
		 * check if the given name is available (known) in a container
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has($name);

		/**
		 * create the dependency by it's identifier (name)
		 *
		 * @param string $name
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function create($name, ...$parameterList);

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
		 *
		 * @return mixed return input instance (input is same as output)
		 */
		public function inject($instance);
	}
