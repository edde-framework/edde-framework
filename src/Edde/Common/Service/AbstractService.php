<?php

	namespace Edde\Common\Service;

	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Service\IService;
	use Edde\Common\Object\Object;

	abstract class AbstractService extends Object implements IService {
		/**
		 * @var IConfigurator[][]
		 */
		static protected $configuratorList = [];
		/**
		 * @var static
		 */
		static protected $instance;

		/**
		 * @inheritdoc
		 */
		static public function registerServiceConfigurator(IConfigurator $configurator): void {
			self::$configuratorList[static::class][] = $configurator;
		}

		/**
		 * @inheritdoc
		 */
		static public function getInstance(): IService {
			if(self::$instance === null) {
				self::$instance = self::createInstance()->setup();
			}
			return self::$instance;
		}

		/**
		 * @inheritdoc
		 */
		static public function createInstance(): IService {
			/**
			 * constructor must be parameter-less
			 */
			$instance = new static();
			/**
			 * register all configurators by the base class name and all interfaces in reverse order
			 */
			foreach(array_reverse(array_merge([static::class], (new \ReflectionClass(static::class))->getInterfaceNames())) as $configurator) {
				if(isset(self::$configuratorList[$configurator])) {
					$instance->addConfigurator(self::$configuratorList[static::class][$configurator]);
				}
			}
			return $instance;
		}
	}
