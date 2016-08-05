<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;

	class SetupHandler extends AbstractSetupHandler {
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(ICacheFactory $cacheFactory) {
			$this->cacheFactory = $cacheFactory;
		}

		static public function create(ICacheFactory $cacheFactory, array $factoryList = []) {
			$setupHandler = new self($cacheFactory);
			$setupHandler->registerFactoryList($factoryList);
			$setupHandler->registerFactoryFallback(FactoryFactory::createFallback());
			return $setupHandler;
		}

		public function createContainer() {
			if ($this->container) {
				throw new RuntimeException(sprintf('Cannot run [%s()] multiple times; something is wrong!', __METHOD__));
			}
			$this->container = new Container($factoryManager = new FactoryManager(), $dependencyFactory = new DependencyFactory($factoryManager, $this->cacheFactory), $this->cacheFactory);
			$factoryManager->registerFactoryList([
				IContainer::class => $this->container,
				IFactoryManager::class => $factoryManager,
				IDependencyFactory::class => $dependencyFactory,
				ICacheFactory::class => $this->cacheFactory,
			]);
			$factoryManager->registerFactoryList($this->factoryList);
			if ($this->factoryFallback) {
				$factoryManager->registerFactoryFallback($this->factoryFallback);
			}
			return $this->container;
		}
	}
