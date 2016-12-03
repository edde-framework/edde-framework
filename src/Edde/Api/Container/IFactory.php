<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Callback\IParameter;

	/**
	 * Factory is general way how to build a dependency with the final set of parameters/dependencies.
	 */
	interface IFactory {
		/**
		 * can this factory handle the given input (class/interface/...)?
		 *
		 * @param string $canHandle
		 *
		 * @return bool
		 */
		public function canHandle(string $canHandle): bool;

		/**
		 * return set of required "hard" dependencies to execute this factory (constructor, lambda, ...)
		 *
		 * @param string $name
		 *
		 * @return array|IParameter[]
		 */
		public function getMandatoryList(string $name): array;

		/**
		 * return set of property name (key) and dependency name (value)
		 *
		 * @param string $name
		 *
		 * @return array
		 */
		public function getInjectList(string $name): array;

		/**
		 * return set of property name (key) and dependency name (value)
		 *
		 * @param string $name
		 *
		 * @return IParameter[]
		 */
		public function getLazyInjectList(string $name): array;
	}
