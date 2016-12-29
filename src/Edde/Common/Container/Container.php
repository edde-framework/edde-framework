<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\ILazyInject;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractContainer {
		/**
		 * @var ICache
		 */
		protected $cache;

		/**
		 * @param ICache $cache
		 */
		public function __construct(ICache $cache) {
			$this->cache = $cache;
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
		 * @throws ContainerException
		 */
		public function inject($instance, IDependency $dependency = null) {
			if (is_object($instance) === false) {
				return $instance;
			}
			/** @var $reflectionProperty \ReflectionProperty */
			foreach ($dependency->getInjectList() as list($reflectionProperty, $name)) {
				$reflectionProperty->setValue($instance, $this->create($name));
			}
			/** @var $reflectionProperty \ReflectionProperty */
			/** @var $instance ILazyInject */
			foreach ($dependency->getLazyList() as list($reflectionProperty, $name)) {
				$instance->lazy($reflectionProperty->getName(), $this, $name);
			}
			return $instance;
		}

		/**
		 * @param IFactory $factory
		 * @param array    $parameterList
		 * @param string   $name
		 *
		 * @return mixed
		 * @throws ContainerException
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null) {
			$dependency = $factory->dependency($this, $name);
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($dependency->getParameterList() as $parameter) {
				if (--$grab >= 0 || $parameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $this->factory($this->getFactory($class = (($class = $parameter->getClass()) ? $class->getName() : $parameter->getName())), [], $class);
			}
			$instance = $this->inject($factory->execute($this, array_merge($parameterList, $dependencyList), $name), $dependency);
			if ($instance instanceof IConfigurable) {
				$instance->registerConfigHandlerList(isset($this->configHandlerList[$name]) ? $this->configHandlerList[$name] : []);
				$instance->init();
			}
			return $instance;
		}
	}
