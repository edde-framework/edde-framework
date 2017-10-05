<?php
	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IService;
	use Edde\Common\Object\Object;

	abstract class AbstractService extends Object implements IService {
		/**
		 * @var IConfigurator[][]
		 */
		static protected $configuratorList = [];
		/**
		 * @var IFactory[]
		 */
		static protected $factory;

		/**
		 * @inheritdoc
		 */
		static public function registerConfigurator(IConfigurator $configurator): void {
			self::$configuratorList[static::class][] = $configurator;
		}

		/**
		 * @inheritdoc
		 */
		static public function registerFactory(IFactory $factory): void {
			self::$factory[static::class] = $factory;
		}

		/**
		 * @inheritdoc
		 */
		static public function factory(): IService {
		}

		/**
		 * @inheritdoc
		 */
		static public function createInstance(): IService {
			if (self::$instance === null) {
				self::$instance = self::create();
			}
			return self::$instance;
		}

		/**
		 * @inheritdoc
		 */
		static public function create(): IService {
			/**
			 * constructor must be parameter-less
			 */
			$instance = new static();
			$instance->setConfiguratorList(self::$configuratorList[static::class] ?? []);
			return $instance;
		}
	}
