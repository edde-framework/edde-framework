<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Serializable\AbstractSerializable;
	use Edde\Ext\Container\CallbackFactory;
	use Edde\Ext\Container\CallbackProxyFactory;

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

		/**
		 * @inheritdoc
		 */
		public function registerFactoryList($factoryList): IFactoryManager {
			$this->factoryList = array_merge($this->factoryList, $factoryList);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function getFactory($dependency): IFactory {
			$this->use();
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($dependency)) {
					return $factory->getFactory();
				}
			}
			throw new FactoryException(sprintf('Cannot find factory for the given dependency [%s].', is_string($dependency) ? $dependency : gettype($dependency)));
		}

		protected function onBootstrap() {
			parent::onBootstrap();
			$factoryList = [];
			/** @var mixed $factory */
			foreach ($this->factoryList as $name => $factory) {
				if (is_array($factory) && is_string(reset($factory))) {
					$factory = new CallbackProxyFactory($name, reset($factory), end($factory), $this);
				} else if (is_callable($factory)) {
					if (is_string($name) === false) {
						$name = (string)ReflectionUtils::getMethodReflection($factory)
							->getReturnType();
					}
					$factory = new CallbackFactory($name, $factory);
				} else if ($factory instanceof IFactory) {
				} else {
					throw new FactoryException(sprintf('Cannot recognize input factory type [%s].', is_string($factory) ? $factoryList : gettype($factory)));
				}
				$factoryList[] = $factory;
				$factory->setCache($this->cache);
			}
			$this->factoryList = $factoryList;
		}
	}
