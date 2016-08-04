<?php
	declare(strict_types = 1);

	namespace Edde\Api\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;

	/**
	 * Standard interface for basic runtime setup.
	 */
	interface ISetupHandler {
		/**
		 * @param IFactory[] $fatoryList
		 *
		 * @return $this
		 */
		public function registerFactoryList(array $fatoryList);

		/**
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function registerFactoryFallback(callable $callback);

		/**
		 * run initial application setup and return system container
		 *
		 * @return IContainer
		 */
		public function createContainer();
	}
