<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\DependencyException;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Usable\AbstractUsable;

	class DependencyFactory extends AbstractUsable implements IDependencyFactory {
		use CacheTrait;
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;
		protected $dependencyList = [];

		/**
		 * @param IFactoryManager $factoryManager
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IFactoryManager $factoryManager, ICacheFactory $cacheFactory) {
			$this->factoryManager = $factoryManager;
			$this->cacheFactory = $cacheFactory;
		}

		public function create($name) {
			$this->use();
			if ($dependency = $this->cache->load($cacheId = ('dependency-list/' . $name))) {
				return $dependency;
			}
			$this->build($name, $dependency = new Dependency($name, false, false));
			return $this->cache->save($cacheId, $dependency);
		}

		protected function build($name, IDependency $root) {
			if ($this->factoryManager->hasFactory($name) === false) {
				return;
			}
			if (isset($this->dependencyList[$name])) {
				throw new DependencyException(sprintf('Detected recursive dependency [%s] in stack [%s].', $name, implode(', ', array_keys($this->dependencyList))));
			}
			$this->dependencyList[$name] = true;
			$factory = $this->factoryManager->getFactory($name);
			foreach ($factory->getParameterList() as $parameter) {
				if ($parameter->hasClass() === false) {
					continue;
				}
				$root->addNode($node = new Dependency($parameter->getClass(), true, $parameter->isOptional()));
				$this->build($parameter->getClass(), $node);
			}
			unset($this->dependencyList[$name]);
		}

		protected function prepare() {
			$this->cache();
		}
	}
