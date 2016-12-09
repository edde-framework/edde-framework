<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	interface IFactoryManager {
		/**
		 * @param iterable $factoryList
		 *
		 * @return IFactoryManager
		 */
		public function registerFactoryList($factoryList): IFactoryManager;

		/**
		 * @param mixed $dependency
		 *
		 * @return IFactory
		 */
		public function getFactory($dependency): IFactory;
	}
