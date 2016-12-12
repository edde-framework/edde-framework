<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	interface IFactoryManager {
		/**
		 * @param array $factoryList
		 * @param callable $deffered
		 *
		 * @return IFactoryManager
		 */
		public function registerFactoryList($factoryList, callable $deffered): IFactoryManager;

		/**
		 * @param mixed $dependency
		 *
		 * @return IFactory
		 */
		public function getFactory($dependency): IFactory;
	}
