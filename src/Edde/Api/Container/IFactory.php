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
		 * @param string $dependency
		 *
		 * @return bool
		 */
		public function canHandle(string $dependency): bool;

		/**
		 * @param string $dependency
		 *
		 * @return IDependency
		 */
		public function dependency(string $dependency = null): IDependency;

		/**
		 * 90% usecase is to return self, but in some rare cases factory can return another factory
		 *
		 * @return IFactory
		 */
		public function getFactory(): IFactory;

		/**
		 * @param array $parameterList
		 * @param string $name
		 *
		 * @return mixed
		 */
		public function execute(array $parameterList, string $name = null);
	}
