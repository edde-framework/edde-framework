<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\DependencyException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Dependency factory is responsible for static dependency analysis.
	 */
	class DependencyFactory extends AbstractDeffered implements IDependencyFactory {
		use CacheTrait;
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;
		/**
		 * stack for checking circular dependencies
		 *
		 * @var array
		 */
		protected $dependencyList = [];

		/**
		 * @param IFactoryManager $factoryManager
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IFactoryManager $factoryManager, ICacheFactory $cacheFactory) {
			$this->factoryManager = $factoryManager;
			$this->cacheFactory = $cacheFactory;
		}

		/**
		 * @inheritdoc
		 * @throws DependencyException
		 * @throws FactoryException
		 */
		public function create(string $name): IDependency {
			$this->use();
			if ($dependency = $this->cache->load($cacheId = ('dependency-list/' . $name))) {
				return $dependency;
			}
			$this->build($name, $dependency = new Dependency($name, false, false, $name));
			return $this->cache->save($cacheId, $dependency);
		}

		/**
		 * @param string $name
		 * @param IDependency $root
		 *
		 * @throws DependencyException
		 * @throws FactoryException
		 */
		protected function build(string $name, IDependency $root) {
			if ($this->factoryManager->hasFactory($name) === false) {
				return;
			}
			if (isset($this->dependencyList[$name])) {
				throw new DependencyException(sprintf('Detected recursive dependency [%s] in stack [%s].', $name, implode(', ', array_keys($this->dependencyList))));
			}
			$this->dependencyList[$name] = true;
			$factory = $this->factoryManager->getFactory($name);
			foreach ($factory->getParameterList($name) as $parameter) {
				$root->addNode($node = new Dependency($parameter->getName(), true, $parameter->isOptional(), $parameter->getClass()));
				if ($parameter->hasClass()) {
					$this->build($parameter->getClass(), $node);
				}
			}
			unset($this->dependencyList[$name]);
		}
	}
