<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	class FactoryFactory extends AbstractObject {
		/**
		 * @param array $factoryList
		 *
		 * @return IFactory[]
		 * @throws FactoryException
		 */
		static public function createList(array $factoryList) {
			$factories = [];
			$singleton = true;
			foreach ($factoryList as $name => $factory) {
				if (is_string($name) === false) {
					$name = $factory;
					$singleton = false;
				}
				$factories[$name] = self::create($name, $factory, $singleton);
				$singleton = true;
			}
			return $factories;
		}

		/**
		 * @param string $name
		 * @param mixed $factory
		 * @param bool $singleton
		 * @param bool $cloneable
		 *
		 * @return IFactory
		 * @throws FactoryException
		 */
		static public function create($name, $factory, $singleton = true, $cloneable = false) {
			if (is_callable($factory)) {
				return new CallbackFactory($name, $factory, $singleton, $cloneable);
			} else if (is_string($factory) && class_exists($factory)) {
				return new ClassFactory($name, $factory, $singleton, $cloneable);
			} else if ($factory instanceof IFactory) {
				return $factory;
			} else if (is_object($factory)) {
				return new InstanceFactory($name, $factory);
			} else if (is_array($factory)) {
				$factory[1]($instance = self::create($name, $factory[0], $singleton, $cloneable));
				return $instance;
			}
			throw new FactoryException(sprintf('Cannot handle [%s] factory, unknown $factory type.', $name));
		}

		/**
		 * @param bool $singleton is default class from callback singleton?
		 *
		 * @return callable
		 * @throws FactoryException
		 */
		static public function createFallback($singleton = false) {
			return function ($name) use ($singleton) {
				if (class_exists($name) === false) {
					return null;
				}
				return FactoryFactory::create($name, $name, $singleton);
			};
		}
	}
