<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;

	/**
	 * Simple factory for "handy" container creation.
	 */
	class ContainerFactory extends AbstractObject {
		/**
		 * simple factory method for default (and simle) container instance
		 *
		 * @param array $factoryList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function create(array $factoryList = []): IContainer {
			$factoryManager = new FactoryManager($cacheFactory = $factoryList[ICacheFactory::class] ?? new CacheFactory(__NAMESPACE__, $factoryList[ICacheStorage::class] ?? new InMemoryCacheStorage()));
			$factoryManager->registerFactoryList($factoryList);
			if (isset($factoryList[ICacheFactory::class]) && is_object($factoryList[ICacheFactory::class]) === false) {
				throw new ContainerException(sprintf('[%s] must be instance (special case).', ICacheFactory::class));
			}
			if (isset($factoryList[ICacheStorage::class]) && is_object($factoryList[ICacheStorage::class]) === false) {
				throw new ContainerException(sprintf('[%s] must be instance (special case).', ICacheStorage::class));
			}
			$container = new Container($factoryManager, $dependencyFactory = new DependencyFactory($factoryManager, $cacheFactory), $cacheFactory);
			$factoryManager->registerFactoryList([
				IContainer::class => $container,
				IFactoryManager::class => $factoryManager,
				IDependencyFactory::class => $dependencyFactory,
				ICacheFactory::class => $cacheFactory,
				new ClassFactory(),
			]);
			return $container;
		}
	}
