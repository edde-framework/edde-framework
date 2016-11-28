<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Utility class for "abstract" cache creation (it hides concrete factories on the background).
	 */
	class FactoryFactory extends AbstractObject {
		/**
		 * @param array $factoryList
		 *
		 * @return IFactory[]
		 * @throws FactoryException
		 */
		static public function createList(array $factoryList): array {
			$factories = [];
			$singleton = true;
			foreach ($factoryList as $name => $factory) {
				if (is_object($factory)) {
					$name = is_string($name) ? $name : get_class($factory);
				} else if (is_string($name) === false) {
					$name = 'anonymous-' . $name;
					if ($factory instanceof IFactory === false) {
						$name = $factory;
						$singleton = false;
					}
				}
				$factories[$name = (string)$name] = self::create($name, $factory, $singleton);
				$singleton = true;
			}
			return $factories;
		}

		/**
		 * @param string $name
		 * @param mixed $factory
		 * @param bool $singleton
		 *
		 * @return IFactory
		 * @throws FactoryException
		 */
		static public function create(string $name, $factory, bool $singleton = true): IFactory {
			if (is_callable($factory)) {
				return new CallbackFactory($name, $factory, $singleton);
			} else if (is_string($factory) && class_exists($factory)) {
				return new ReflectionFactory($name, $factory, $singleton);
			} else if ($factory instanceof IFactory) {
				return $factory;
			} else if (is_object($factory)) {
				return new InstanceFactory($name, $factory);
			}
			throw new FactoryException(sprintf('Cannot handle [%s] factory -  cannot determine factory type of $factory [%s].', $name, is_string($factory) ? $factory : gettype($factory)));
		}
	}
