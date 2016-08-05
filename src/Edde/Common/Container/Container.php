<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Usable\AbstractUsable;

	class Container extends AbstractUsable implements IContainer {
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;
		/**
		 * @var IDependencyFactory
		 */
		protected $dependencyFactory;
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var ICache
		 */
		protected $cache;

		/**
		 * @param IFactoryManager $factoryManager
		 * @param IDependencyFactory $dependencyFactory
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IFactoryManager $factoryManager, IDependencyFactory $dependencyFactory, ICacheFactory $cacheFactory) {
			$this->factoryManager = $factoryManager;
			$this->dependencyFactory = $dependencyFactory;
			$this->cacheFactory = $cacheFactory;
		}

		public function has($name) {
			return $this->factoryManager->hasFactory($name);
		}

		public function inject($instance) {
			$this->usse();
			if (($reflection = $this->cache->load($cacheId = ('reflection/' . get_class($instance)))) === null) {
				$reflectionClass = new \ReflectionClass($instance);
				$methodList = [];
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					$name = $reflectionMethod->getName();
					if ($reflectionMethod->getNumberOfParameters() > 0 && strpos($name, 'inject') !== false && strlen($name) > 6) {
						$methodList[$name] = $name;
					}
				}
				$parent = $reflectionClass;
				$lazyInject = false;
				while ($parent) {
					if (($lazyInject = in_array(LazyInjectTrait::class, $parent->getTraitNames(), true)) === true) {
						break;
					}
					$parent = $parent->getParentClass();
				}
				$this->cache->save($cacheId, $reflection = [
					'method-list' => $methodList,
					'lazy-inject' => $lazyInject,
				]);
			}
			if ($reflection['lazy-inject']) {
				$instance->lazy($this);
			}
			foreach ($reflection['method-list'] as $method) {
				$this->call([
					$instance,
					$method,
				]);
			}
			return $this;
		}

		public function call(callable $callable, ...$parameterList) {
			$this->usse();
			$callback = new Callback($callable);
			$dependencies = [];
			$grab = count($dependencyList = $callback->getParameterList()) - count($parameterList);
			foreach ($dependencyList as $dependency) {
				if ($grab-- <= 0 || $dependency->isOptional() || $dependency->hasClass() === false) {
					break;
				}
				$dependencies[] = $this->create($dependency->getClass());
			}
			return $callback->invoke(...array_merge($dependencies, $parameterList));
		}

		public function create($name, ...$parameterList) {
			$this->usse();
			return $this->factory($this->dependencyFactory->create($name), $parameterList);
		}

		protected function factory(IDependency $root, array $parameterList) {
			$dependencyList = [];
			foreach ($root->getDependencyList() as $dependency) {
				if ($this->factoryManager->hasFactory($dependency->getName()) === false) {
					break;
				}
				$dependencyList[] = $this->factory($dependency, []);
			}
			return $this->factoryManager->getFactory($name = $root->getName())
				->create($name, array_merge($dependencyList, $parameterList), $this);
		}

		protected function prepare() {
			$this->cache = $this->cacheFactory->factory(self::class);
		}
	}
