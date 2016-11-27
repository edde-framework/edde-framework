<?php
	declare(strict_types = 1);

	namespace Edde\Api\Runtime;

	use Edde\Api\Container\IContainer;

	interface IRuntime {
		/**
		 * register factory list used for runtime
		 *
		 * @param array $factoryList
		 *
		 * @return IRuntime
		 */
		public function registerFactoryList(array $factoryList): IRuntime;

		/**
		 * register deffered callback on the given factory name (must be already registered)
		 *
		 * @param string $name
		 * @param callable $onSetup
		 *
		 * @return IRuntime
		 */
		public function deffered(string $name, callable $onSetup): IRuntime;

		/**
		 * create default system container for this runtime
		 *
		 * @return IContainer
		 */
		public function createContainer(): IContainer;

		/**
		 * execute the given callback with the given runtime (some kind of "main" in C)
		 *
		 * @param callable $callback
		 *
		 * @return mixed
		 */
		public function run(callable $callback);

		/***
		 * @return bool
		 */
		public function isConsoleMode(): bool;
	}
