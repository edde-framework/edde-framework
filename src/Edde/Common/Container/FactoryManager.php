<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\Factory\FactoryFactory;

	class FactoryManager extends AbstractObject implements IFactoryManager {
		/**
		 * @var IFactory[]
		 */
		private $factoryList = [];
		/**
		 * @var callable
		 */
		private $factoryFallback;

		public function registerFactoryList(array $factoryList) {
			foreach (FactoryFactory::createList($factoryList) as $name => $factory) {
				$this->registerFactory($name, $factory);
			}
			return $this;
		}

		public function registerFactory($name, IFactory $factory) {
			$this->factoryList[$name] = $factory;
			return $this;
		}

		public function registerFactoryFallback(callable $callback) {
			$this->factoryFallback = $callback;
			return $this;
		}

		public function getFactory($name) {
			if ($this->hasFactory($name) === false) {
				if ($this->factoryFallback && ($factory = call_user_func($this->factoryFallback, $name)) !== null) {
					return $this->factoryList[$name] = $factory;
				}
				throw new FactoryException(sprintf('Requested unknown factory [%s].', $name));
			}
			return $this->factoryList[$name];
		}

		public function hasFactory($name) {
			return isset($this->factoryList[$name]);
		}
	}
