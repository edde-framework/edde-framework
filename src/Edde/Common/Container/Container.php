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
	use Edde\Ext\Container\ClassFactory;

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
			if (($id = $this->cache->load($cacheId = ('factory/' . $dependency))) !== null) {
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
		 * @param IFactory $factory
		 * @param array    $parameterList
		 * @param string   $name
		 *
		 * @return mixed
		 * @throws ContainerException
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null) {
			if (($instance = $factory->fetch($this, $fetchId = (get_class($factory) . count($parameterList) . $name), $this->cache)) !== null) {
				return $instance;
			}
			if (($dependency = $this->cache->load($cacheId = ('dependency/' . $name))) === null) {
				$this->cache->save($cacheId, $dependency = $factory->dependency($this, $name));
			}
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($dependency->getParameterList() as $parameter) {
				if (--$grab >= 0 || $parameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $this->factory($this->getFactory($class = (($class = $parameter->getClass()) ? $class->getName() : $parameter->getName())), [], $class);
			}
			$this->dependency($instance = $factory->execute($this, array_merge($parameterList, $dependencyList), $name), $dependency);
			if ($instance instanceof IConfigurable) {
				/** @var $instance IConfigurable */
				$instance->registerConfigHandlerList(isset($this->configHandlerList[$name]) ? $this->configHandlerList[$name] : []);
				$instance->init();
			}
			$factory->push($this, $fetchId, $instance, $this->cache);
			return $instance;
		}

		/**
		 * @inheritdoc
		 */
		public function autowire($instance, bool $force = false) {
			if (is_object($instance) === false) {
				return $instance;
			}
			if (($dependency = $this->cache->load($cacheId = ('dependency/' . ($class = get_class($instance))))) === null) {
				$classFactory = new ClassFactory();
				$this->cache->save($cacheId, $dependency = $classFactory->dependency($this, $class));
			}
			$this->dependency($instance, $dependency, $force !== true);
			return $instance;
		}

		/**
		 * @param mixed       $instance
		 * @param IDependency $dependency
		 * @param bool        $lazy
		 *
		 * @return ILazyInject
		 */
		protected function dependency($instance, IDependency $dependency, bool $lazy = true) {
			if (is_object($instance) === false) {
				return $instance;
			}
			/** @var $instance ILazyInject */
			/** @var $reflectionProperty \ReflectionProperty */
			foreach ($dependency->getInjectList() as list($reflectionProperty, $name)) {
				$instance->inject($reflectionProperty->getName(), $this->create($name));
			}
			/** @var $reflectionProperty \ReflectionProperty */
			foreach ($dependency->getLazyList() as list($reflectionProperty, $name)) {
				if ($lazy) {
					$instance->lazy($reflectionProperty->getName(), $this, $name);
					continue;
				}
				$instance->inject($reflectionProperty->getName(), $this->create($name));
			}
			return $instance;
		}
	}
