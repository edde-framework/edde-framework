<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\Cache;
	use Edde\Common\Container\Container;
	use Edde\Ext\Cache\InMemoryCacheStorage;

	class ContainerFactory extends AbstractObject {
		static public function createFactoryList(array $factoryList): array {
			$factories = [];
			foreach ($factoryList as $name => $factory) {
				$current = null;
				if (is_string($name) && is_string($factory) && interface_exists($name)) {
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
					throw new FactoryException(sprintf('Class instances are not supported in factory definition [%s; %s].', $name, get_class($factory)));
				}
				if ($current === null) {
					throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
				}
				$factories[] = $current;
			}
			return $factories;
		}

		static public function create(array $factoryList = [], string $temp = null): IContainer {
			$cacheId = sha1(implode('', array_keys($factoryList)));
			if (file_exists($cache = ($temp ?: sys_get_temp_dir()) . '/container-' . $cacheId . '.edde')) {
				/** @noinspection UnserializeExploitsInspection */
				return unserialize(file_get_contents($cache));
			}
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			$container->registerFactoryList($factoryList = self::createFactoryList($factoryList));
			$container = $container->create(IContainer::class)
				->registerFactoryList($factoryList);
			file_put_contents($cache, serialize($container));
			return $container;
		}
	}
