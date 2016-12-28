<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\Cache;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Ext\Cache\InMemoryCacheStorage;

	class ContainerFactory extends AbstractObject {
		static public function createFactoryList(array $factoryList): array {
			$factories = [];
			foreach ($factoryList as $name => $factory) {
				$current = null;
				if (is_string($factory) && strpos($factory, '::') !== false) {
					list($target, $method) = explode('::', $factory);
					$current = new ProxyFactory($name, $target, $method);
				} else if (is_string($name) && is_string($factory) && interface_exists($name)) {
					if (class_exists($factory)) {
						$current = new InterfaceFactory($name, $factory);
					} else if (interface_exists($factory)) {
						$current = new LinkFactory($name, $factory);
					}
				} else if ($factory instanceof IFactory) {
					$current = $factory;
				} else if (is_callable($factory)) {
					throw new FactoryException(sprintf('Closures are not supported in factory definition [%s].', $name));
				} else if (is_object($factory)) {
					if ($factory instanceof \Serializable === false) {
						throw new FactoryException(sprintf('Class instances [%s] are not supported in factory definition [%s]. You can use [%s] interface to bypass this error.', get_class($factory), $name, \Serializable::class));
					}
					$current = new SerializableFactory($name, $factory);
				}
				if ($current === null) {
					throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
				}
				$factories[] = $current;
			}
			return $factories;
		}

		/**
		 * pure way how to simple create a system container using another container
		 *
		 * @param array    $factoryList
		 * @param string[] $configHandlerList
		 *
		 * @return IContainer
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		static public function create(array $factoryList = [], array $configHandlerList = []): IContainer {
			/**
			 * A young man and his date were parked on a back road some distance from town.
			 * They were about to have sex when the girl stopped.
			 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
			 * The man reluctantly paid her, and they did their thing.
			 * After a cigarette, the man just sat in the driver’s seat looking out the window.
			 * “Why aren’t we going anywhere?” asked the girl.
			 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
			 */
			/** @var $container IContainer */
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			$container->registerFactoryList($factoryList = self::createFactoryList($factoryList));
			$container = $container->create(IContainer::class);
			$container->registerFactoryList($factoryList);
			foreach ($configHandlerList as $name => $configHandler) {
				foreach ($configHandler as $config) {
					$container->registerConfigHandler($name, $container->create($config));
				}
			}
			return $container;
		}

		/**
		 * create a default container with set of services from Edde; they can be simply redefined
		 *
		 * @param array    $factoryList
		 * @param string[] $configHandlerList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function container(array $factoryList = [], array $configHandlerList = []): IContainer {
			return self::create(array_merge([
				IContainer::class => Container::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
				ICacheManager::class => CacheManager::class,
				ICache::class => ICacheManager::class,
			], $factoryList), array_merge([], $configHandlerList));
		}

		/**
		 * create container and serialize the result into the file; if file exists, container is build from it
		 *
		 * @param array  $factoryList
		 * @param array  $configHandlerList
		 * @param string $cache
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function cache(array $factoryList, array $configHandlerList, string $cache): IContainer {
			if ($container = @file_get_contents($cache)) {
				/** @noinspection UnserializeExploitsInspection */
				return unserialize($container);
			}
			file_put_contents($cache, serialize($container = self::container($factoryList, $configHandlerList)));
			return $container;
		}
	}
