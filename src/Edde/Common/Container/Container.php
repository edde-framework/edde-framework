<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\Factory\ClassFactory;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractContainer {
		/**
		 * @var \SplStack
		 */
		protected $stack;

		/**
		 * One day, Little Johnny saw his grandpa smoking his cigarettes. Little Johnny asked,
		 * "Grandpa, can I smoke some of your cigarettes?" His grandpa replied,
		 * "Can your penis reach your asshole?"
		 * "No", said Little Johnny.
		 * His grandpa replied,
		 * "Then you're not old enough."
		 *
		 * The next day, Little Johnny saw his grandpa drinking beer. He asked,
		 * "Grandpa, can I drink some of your beer?"
		 * His grandpa replied,
		 * "Can your penis reach your asshole?"
		 * "No" said Little Johhny.
		 * "Then you're not old enough." his grandpa replied.
		 *
		 * The next day, Little Johnny was eating cookies.
		 * His grandpa asked, "Can I have some of your cookies?"
		 * Little Johnny replied, "Can your penis reach your asshole?"
		 * His grandpa replied, "It most certainly can!"
		 * Little Johnny replied, "Then go fuck yourself.
		 */
		public function __construct() {
			$this->stack = new \SplStack();
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function getFactory(string $dependency, string $source = null): IFactory {
			foreach ($this->factoryList as $id => $factory) {
				if ($factory->canHandle($this, $dependency)) {
					return $factory->getFactory($this);
				}
			}
			throw new UnknownFactoryException(sprintf('Unknown factory [%s] for dependency [%s]; dependency chain [%s].', $dependency, $source ?: 'unknown source', implode('→', array_reverse(iterator_to_array($this->stack)))));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null, string $source = null) {
			try {
				$this->stack->push($name ?: '[anonymous]');
				if (($instance = $factory->fetch($this, $fetchId = (get_class($factory) . count($parameterList) . $name . $source))) !== null) {
					return $instance;
				}
				$this->dependency($instance = $factory->execute($this, $parameterList, $dependency = $factory->createDependency($this, $name), $name), $dependency);
				if ($instance instanceof IConfigurable) {
					/** @var $instance IConfigurable */
					$configuratorList = [];
					foreach ($dependency->getConfiguratorList() as $configurator) {
						if (isset($this->configuratorList[$configurator])) {
							$configuratorList = array_merge($configuratorList, $this->configuratorList[$configurator]);
						}
					}
					$instance->setConfiguratorList($configuratorList);
					$instance->init();
				}
				$factory->push($this, $fetchId, $instance);
				return $instance;
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
			return $this->dependency($instance, (new ClassFactory())->createDependency($this, $class = get_class($instance)), $force !== true);
		}

		/**
		 * @param mixed       $instance
		 * @param IDependency $dependency
		 * @param bool        $lazy
		 *
		 * @return ILazyInject
		 * @throws ContainerException
		 * @throws FactoryException
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
				$instance->inject($reflectionParameter->getName(), $this->create($reflectionParameter->getClass(), [], $class));
			}
			return $instance;
		}
	}
