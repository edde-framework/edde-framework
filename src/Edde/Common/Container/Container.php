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
		 * @var \SplStack
		 */
		protected $stack;

		/**
		 * @param ICache $cache
		 */
		public function __construct(ICache $cache) {
			parent::__construct($cache);
			$this->stack = new \SplStack();
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function getFactory(string $dependency): IFactory {
			if (($id = $this->cache->load($cacheId = (__METHOD__ . '/' . $dependency))) !== null) {
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
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null, string $source = null) {
			try {
				$this->stack->push($name ?: '[anonymous]');
				if (($instance = $factory->fetch($this, $fetchId = (get_class($factory) . count($parameterList) . $name), $this->cache)) !== null) {
					return $instance;
				}
				if (($dependency = $this->cache->load($cacheId = (__METHOD__ . '/' . $name))) === null) {
					$this->cache->save($cacheId, $dependency = $factory->dependency($this, $name));
				}
				$grab = count($parameterList);
				$dependencyList = [];
				foreach ($dependency->getParameterList() as $reflectionParameter) {
					if (--$grab >= 0 || $reflectionParameter->isOptional()) {
						continue;
					}
					$dependencyList[] = $this->factory($this->getFactory($class = (($class = $reflectionParameter->getClass()) ? $class : $reflectionParameter->getName())), [], $class, $name);
				}
				$this->dependency($instance = $factory->execute($this, array_merge($parameterList, $dependencyList), $name), $dependency);
				if ($instance instanceof IConfigurable) {
					/** @var $instance IConfigurable */
					$instance->registerConfigHandlerList(isset($this->configHandlerList[$name]) ? $this->configHandlerList[$name] : []);
					$instance->init();
				}
				$factory->push($this, $fetchId, $instance, $this->cache);
				return $instance;
			} catch (FactoryException $exception) {
				throw new ContainerException(sprintf('Cannot create the given dependency [%s] for [%s]; dependency chain [%s].', $name, $source ?: 'unknown source', implode('→', array_reverse(iterator_to_array($this->stack)))), 0, $exception);
			} catch (ContainerException $exception) {
				throw $exception;
			} catch (\Exception $exception) {
				throw new ContainerException(sprintf('Cannot create the given dependency [%s] for [%s]; dependency chain [%s].', $name, $source ?: 'unknown source', implode('→', array_reverse(iterator_to_array($this->stack)))), 0, $exception);
			} finally {
				$this->stack->pop();
			}
		}

		/**
		 * @inheritdoc
		 */
		public function autowire($instance, bool $force = false) {
			if (is_object($instance) === false) {
				return $instance;
			}
			if (($dependency = $this->cache->load($cacheId = (__METHOD__ . '/' . ($class = get_class($instance))))) === null) {
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
			$class = get_class($instance);
			/** @var $instance ILazyInject */
			foreach ($dependency->getInjectList() as $reflectionParameter) {
				$instance->inject($reflectionParameter->getName(), $this->create($reflectionParameter->getClass(), [], $class));
			}
			foreach ($dependency->getLazyList() as $reflectionParameter) {
				if ($lazy) {
					$instance->lazy($reflectionParameter->getName(), $this, $reflectionParameter->getClass());
					continue;
				}
				$instance->inject($reflectionParameter->getName(), $reflectionParameter->getClass());
			}
			return $instance;
		}
	}
