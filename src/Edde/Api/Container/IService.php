<?php
	namespace Edde\Api\Container;

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
		static public function registerConfigurator(IConfigurator $configurator): void;

		/**
		 * register a factory responsible for service creation
		 *
		 * @param IFactory $factory
		 */
		static public function registerFactory(IFactory $factory): void;

		/**
		 * create and setup an instance
		 *
		 * @return self
		 */
		static public function instance(): IService;

		/**
		 * create but do not setup instance
		 *
		 * @return static
		 */
		static public function createInstance(): IService;

		/**
		 * retrieve current instance; should be implemented in all target services to
		 * keep proper type hints for IDE (yoyo, PHP still don't have fuckin' generics...)
		 *
		 * this method should only call self::instance() internally, eventually self::create if a class
		 * is not singleton
		 *
		 * @return self
		 */
		static public function get();
	}
