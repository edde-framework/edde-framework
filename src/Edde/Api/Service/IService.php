<?php

	namespace Edde\Api\Service;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Config\IConfigurator;

	interface IService extends IConfigurable {
		/**
		 * register configurator for this service; as it is static, all configurators are bound to the given instance;
		 * when a new instance is created (by createInstance), configurators are also bound
		 *
		 * @param IConfigurator $configurator
		 *
		 * @return void
		 */
		static public function registerServiceConfigurator(IConfigurator $configurator): void;

		/**
		 * create and setup an instance
		 *
		 * @return self
		 */
		static public function getInstance(): IService;

		/**
		 * create but do not setup instance
		 *
		 * @return static
		 */
		static public function createInstance(): IService;
	}
