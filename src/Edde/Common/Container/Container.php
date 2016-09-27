<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractDeffered implements IContainer {
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

		/**
		 * @inheritdoc
		 */
		public function registerFactory(string $name, IFactory $factory): IContainer {
			$this->factoryManager->registerFactory($name, $factory);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has($name) {
			return $this->factoryManager->hasFactory($name);
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function inject($instance) {
			$this->use();
			if (($reflection = $this->cache->load($cacheId = ('reflection/' . get_class($instance)))) === null) {
				$reflectionClass = new \ReflectionClass($instance);
				$methodList = [];
				$injectList = [];
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					$name = $reflectionMethod->getName();
					if ($reflectionMethod->getNumberOfParameters() > 0) {
						if (strpos($name, 'inject') !== false && strlen($name) > 6) {
							$methodList[$name] = $name;
						}
						if (strpos($name, 'lazy') !== false && strlen($name) > 4) {
							$parameterList = [];
							foreach ($reflectionMethod->getParameters() as $parameter) {
								if ($reflectionClass->hasProperty($parameter->getName()) === false) {
									throw new ContainerException(vsprintf("Lazy inject missmatch: parameter [$%s] of method [%s::%s()] must have a property [%s::$%s] with the same name as the paramete (for example protected \$%s).", [
										$parameter->getName(),
										$reflectionClass->getName(),
										$reflectionMethod->getName(),
										$reflectionClass->getName(),
										$parameter->getName(),
										$parameter->getName(),
									]));
								}
								$parameterList[$parameter->getName()] = $parameter->getClass()
									->getName();
							}
							$injectList[$name] = $parameterList;
						}
					}
				}
				if (in_array(ILazyInject::class, $reflectionClass->getInterfaceNames(), true) === false) {
					$injectList = [];
				}
				$this->cache->save($cacheId, $reflection = [
					'method-list' => $methodList,
					'lazy-inject' => $injectList,
				]);
			}
			/** @noinspection ForeachSourceInspection */
			/** @var $instance ILazyInject */
			foreach ($reflection['lazy-inject'] as $method) {
				/** @noinspection ForeachSourceInspection */
				foreach ($method as $property => $class) {
					$instance->lazy($property, function () use ($class) {
						return $this->create($class);
					});
				}
			}
			/** @noinspection ForeachSourceInspection */
			foreach ($reflection['method-list'] as $method) {
				$this->call([
					$instance,
					$method,
				]);
			}
			return $instance;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function create($name, ...$parameterList) {
			$this->use();
			return $this->factory($this->dependencyFactory->create($name), $parameterList);
		}

		/**
		 * @param IDependency $root
		 * @param array $parameterList
		 *
		 * @return mixed
		 * @throws FactoryException
		 */
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

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function call(callable $callable, ...$parameterList) {
			$this->use();
			$callback = new Callback($callable);
			$dependencies = [];
			$grab = count($dependencyList = $callback->getParameterList()) - count($parameterList);
			/** @var $dependencyList IParameter[] */
			foreach ($dependencyList as $dependency) {
				if ($grab-- <= 0 || $dependency->isOptional() || $dependency->hasClass() === false) {
					break;
				}
				$dependencies[] = $this->create($dependency->getClass());
			}
			return $callback->invoke(...array_merge($dependencies, $parameterList));
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->cache = $this->cacheFactory->factory(self::class);
		}
	}
