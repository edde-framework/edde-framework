<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Ext\Cache\DevNullCacheStorage;

	/**
	 * Simple factory for "handy" container creation.
	 */
	class ContainerFactory extends AbstractObject {
		static public function create(array $factoryList = []): IContainer {
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$factoryManager->registerFactoryList($factoryList);
			$container = new Container($factoryManager, $dependencyFactory = new DependencyFactory($factoryManager, $cacheFactory = $factoryList[ICacheFactory::class] ?? new CacheFactory(__NAMESPACE__, $factoryList[ICacheStorage::class] ?? new DevNullCacheStorage())), $cacheFactory);
			$factoryManager->registerFactoryList([
				IContainer::class => $container,
				IFactoryManager::class => $factoryManager,
				IDependencyFactory::class => $dependencyFactory,
				ICacheFactory::class => $cacheFactory,
			]);
			return $container;
		}
	}
