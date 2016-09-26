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
		protected $handleList = [];

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
		public function getFactory(string $name): IFactory {
			if (isset($this->handleList[$name])) {
				return $this->handleList[$name];
			}
			if ($this->hasFactory($name) === false) {
				throw new FactoryException(sprintf('Requested unknown factory [%s].', $name));
			}
			if (isset($this->factoryList[$name])) {
				$factory = $this->factoryList[$name];
				if ($factory->canHandle($name) === false) {
					throw new FactoryException(sprintf('Requested factory cannot handle identifier [%s].', $name));
				}
				return $this->handleList[$name] = $factory;
			}
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($name)) {
					return $this->handleList[$name] = $factory;
				}
			}
			throw new FactoryException(sprintf('Some strange bug here for factory [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		public function hasFactory(string $name): bool {
			if (isset($this->factoryList[$name])) {
				return true;
			}
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($name)) {
					return true;
				}
			}
			return false;
		}
	}
