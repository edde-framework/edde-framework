<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Ext\Container\CallbackFactory;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractObject implements IContainer {
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
		public function registerFactory(IFactory $factory): IContainer {
			$this->factoryList[] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactoryList(array $factoryList): IContainer {
			$this->factoryList = $factoryList;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function getFactory(string $dependency): IFactory {
			if (($id = $this->cache->load($cacheId = ('dependency/' . $dependency))) !== null) {
				return $this->factoryList[$id]->getFactory($this);
			}
			foreach ($this->factoryList as $id => $factory) {
				if ($factory->canHandle($this, $dependency)) {
					$this->cache->save($cacheId, $id);
					return $factory->getFactory($this);
				}
			}
			throw new FactoryException(sprintf('Cannot find factory for the given dependency [%s].', $dependency));
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, ...$parameterList) {
			return $this->factory($this->getFactory($name), $parameterList, $name);
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function call(callable $callable, ...$parameterList) {
			return $this->factory(new CallbackFactory($callable), $parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function inject($instance, IDependency $dependency = null) {
			if (is_object($instance) === false) {
				return $instance;
			}
			foreach ($dependency->getInjectList() as $injectList) {
				/** @var $reflectionParameter \ReflectionParameter */
				/** @var $reflectionProperty \ReflectionProperty */
				/** @noinspection ForeachSourceInspection */
				foreach ($injectList as list($reflectionParameter, $reflectionProperty)) {
					$reflectionProperty->setValue($instance, $this->create(($class = $reflectionParameter->getClass()) ? $class->getName() : $reflectionParameter->getName()));
				}
			}
			foreach ($dependency->getLazyList() as $lazyList) {
				/** @var $reflectionParameter \ReflectionParameter */
				/** @var $reflectionProperty \ReflectionProperty */
				/** @noinspection ForeachSourceInspection */
				foreach ($lazyList as list($reflectionParameter, $reflectionProperty)) {
					$instance->lazy($reflectionProperty->getName(), function () use ($reflectionParameter) {
						return $this->create(($class = $reflectionParameter->getClass()) ? $class->getName() : $reflectionParameter->getName());
					});
				}
			}
			return $instance;
		}

		/**
		 * @param IFactory $factory
		 * @param array $parameterList
		 * @param string $name
		 *
		 * @return mixed
		 * @throws ContainerException
		 */
		protected function factory(IFactory $factory, array $parameterList = [], string $name = null) {
			$dependency = $factory->dependency($this, $name);
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($dependency->getParameterList() as $parameter) {
				if (--$grab >= 0) {
					continue;
				}
				$dependencyList[] = $this->factory($this->getFactory($class = (($class = $parameter->getClass()) ? $class->getName() : $parameter->getName())), [], $class);
			}
			return $this->inject($factory->execute($this, array_merge($parameterList, $dependencyList), $name), $dependency);
		}
	}
