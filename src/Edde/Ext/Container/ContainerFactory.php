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

		static public function create(array $factoryList = []): IContainer {
			/**
			 * A young man and his date were parked on a back road some distance from town.
			 * They were about to have sex when the girl stopped.
			 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
			 * The man reluctantly paid her, and they did their thing.
			 * After a cigarette, the man just sat in the driver’s seat looking out the window.
			 * “Why aren’t we going anywhere?” asked the girl.
			 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
			 */
			return (new Container(new Cache(new InMemoryCacheStorage())))->registerFactoryList($factoryList = self::createFactoryList($factoryList))
				->create(IContainer::class)
				->registerFactoryList($factoryList);
		}
	}
