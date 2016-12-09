<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Serializable\AbstractSerializable;

	/**
	 * Default implementation of a cache manager.
	 */
	class FactoryManager extends AbstractSerializable implements IFactoryManager {
		/**
		 * @var ICache
		 */
		protected $cache;
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];

		/**
		 * @param ICache $cache
		 */
		public function __construct(ICache $cache) {
			$this->cache = $cache;
		}

		public function registerFactoryList($factoryList): IFactoryManager {
			$this->factoryList = array_merge($this->factoryList, $factoryList);
			return $this;
		}

		public function getFactory($dependency): IFactory {
			$this->use();
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($dependency)) {
					return $factory;
				}
			}
			throw new FactoryException(sprintf('Cannot find factory for the given dependency [%s].', is_string($dependency) ? $dependency : gettype($dependency)));
		}

		protected function onBootstrap() {
			parent::onBootstrap();
			$factoryList = [];
			foreach ($this->factoryList as $name => $factory) {
				if ($factory instanceof IFactory) {
					$factoryList[] = $factory;
				}
				$factory->setCache($this->cache);
			}
			$this->factoryList = $factoryList;
		}
	}
