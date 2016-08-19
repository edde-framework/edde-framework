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
		 * @return ISetupHandler
		 */
		public function registerFactoryList(array $fatoryList): ISetupHandler;

		/**
		 * @param callable $callback
		 *
		 * @return ISetupHandler
		 */
		public function registerFactoryFallback(callable $callback): ISetupHandler;

		/**
		 * attach onSetup handler to a given class/identifier (it must be IUsable)
		 *
		 * @param string $name
		 * @param callable $onSetup
		 *
		 * @return ISetupHandler
		 */
		public function onSetup(string $name, callable $onSetup): ISetupHandler;

		/**
		 * run initial application setup and return system container
		 *
		 * @return IContainer
		 */
		public function createContainer(): IContainer;
	}
