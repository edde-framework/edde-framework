<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\Object;
	use Edde\Ext\Container\CallbackFactory;

	abstract class AbstractContainer extends Object implements IContainer {
		/**
		 * @var ICache
		 */
		protected $cache;
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var \Edde\Api\Config\IConfigurator[][]
		 */
		protected $configHandlerList = [];

		/**
		 * @param ICache $cache
		 */
		public function __construct(ICache $cache) {
			$this->cache = $cache;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactory(IFactory $factory, string $id = null): IContainer {
			if ($id) {
				$this->factoryList[$id] = $factory;
				return $this;
			}
			$this->factoryList[] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactoryList(array $factoryList): IContainer {
			$this->factoryList = [];
			foreach ($factoryList as $id => $factory) {
				$this->registerFactory($factory, is_string($id) ? $id : null);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfigHandler(string $name, \Edde\Api\Config\IConfigurator $configHandler): IContainer {
			$this->configHandlerList[$name][] = $configHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfigHandlerList(array $configHandlerList): IContainer {
			$this->configHandlerList = [];
			foreach ($configHandlerList as $name => $configHandler) {
				$this->registerConfigHandler($name, $configHandler);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, array $parameterList = [], string $source = null) {
			return $this->factory($this->getFactory($name, $source), $parameterList, $name, $source);
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function call(callable $callable, array $parameterList = [], string $source = null) {
			return $this->factory(new CallbackFactory($callable), $parameterList, $source);
		}
	}
