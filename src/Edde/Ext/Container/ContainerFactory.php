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
				}
				if ($current === null) {
					throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : $name, is_string($factory) ? $factory : $factory));
				}
				$factories[] = $current;
			}
			return $factories;
		}

		static public function crate(array $factoryList = [], string $cacheId = null): IContainer {
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			$container->registerFactoryList($factoryList = self::createFactoryList($factoryList));
			return $container->create(IContainer::class);
		}
	}
