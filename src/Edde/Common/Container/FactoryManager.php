<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\Factory\FactoryFactory;

	/**
	 * Default implementation of a factory manager.
	 */
	class FactoryManager extends AbstractObject implements IFactoryManager {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var callable
		 */
		protected $factoryFallback;

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $factoryList): IFactoryManager {
			foreach (FactoryFactory::createList($factoryList) as $name => $factory) {
				$this->registerFactory($name, $factory);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactory(string $name, IFactory $factory): IFactoryManager {
			$this->factoryList[$name] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactoryFallback(callable $callback): IFactoryManager {
			$this->factoryFallback = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getFactory(string $name): IFactory {
			if ($this->hasFactory($name) === false) {
				if ($this->factoryFallback && ($factory = call_user_func($this->factoryFallback, $name)) !== null) {
					return $this->factoryList[$name] = $factory;
				}
				throw new FactoryException(sprintf('Requested unknown factory [%s].', $name));
			}
			return $this->factoryList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function hasFactory(string $name): bool {
			return isset($this->factoryList[$name]);
		}
	}
