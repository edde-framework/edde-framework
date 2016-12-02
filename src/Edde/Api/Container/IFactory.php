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
		 * @return IParameter[]|iterable
		 */
		public function getMandatoryList(string $name): iterable;
	}
