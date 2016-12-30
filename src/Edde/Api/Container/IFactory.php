<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * Factory is general way how to build a dependency with the final set of parameters/dependencies.
	 */
	interface IFactory {
		/**
		 * is this factory able to handle the given input?
		 *
		 * @param IContainer $container
		 *
		 * @param string     $dependency
		 *
		 * @return bool
		 */
		public function canHandle(IContainer $container, string $dependency): bool;

		/**
		 * @param IContainer $container
		 * @param string     $dependency
		 *
		 * @return IDependency
		 */
		public function dependency(IContainer $container, string $dependency = null): IDependency;

		/**
		 * 90% usecase is to return self, but in some rare cases factory can return another factory
		 *
		 * @param IContainer $container
		 *
		 * @return IFactory
		 */
		public function getFactory(IContainer $container): IFactory;

		/**
		 * try to prefetch dependency before heavy computations are done
		 *
		 * @param IContainer $container
		 * @param string     $name
		 * @param array      $parameterList
		 *
		 * @return mixed|null if null is returned, container will execute... execute() on this factory
		 */
		public function fetch(IContainer $container, string $name, array $parameterList);

		/**
		 * @param IContainer $container
		 * @param array      $parameterList
		 * @param string     $name
		 *
		 * @return mixed
		 */
		public function execute(IContainer $container, array $parameterList, string $name = null);
	}
