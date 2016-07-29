<?php
	namespace Edde\Ext\Container;

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
		static public function create(array $factoryList = []) {
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$factoryManager->registerFactoryList($factoryList);
			return new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory = new CacheFactory(__NAMESPACE__, new DevNullCacheStorage())), $cacheFactory);
		}
	}
