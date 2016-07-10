<?php
	namespace Edde\Api\Container;

	use Edde\Api\Usable\IUsable;

	/**
	 * Implementation of Dependency Inject Container.
	 */
	interface IContainer extends IUsable {
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
		 * @return $this
		 */
		public function inject($instance);
	}
