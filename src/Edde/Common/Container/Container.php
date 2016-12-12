<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Ext\Container\CallbackFactory;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractObject implements IContainer {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];

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
			foreach ($factoryList as $factory) {
				$this->registerFactory($factory);
			}
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

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function inject($instance, IFactory $factory = null) {
			if (is_object($instance) === false) {
				return $instance;
			}
			$this->use();
			$factory = $factory ?: $this->getFactory($instance);
			$dependency = $factory->dependency($instance);
			return $instance;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, ...$parameterList) {
			$this->use();
			return $this->factory($this->getFactory($name), $name, $parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function call(callable $callable, ...$parameterList) {
			$this->use();
			return $this->factory(new CallbackFactory($callable), null, $parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function factory(IFactory $factory, string $name = null, array $parameterList = []) {
			/** @var $parameters \ReflectionParameter[] */
			$dependency = $factory->dependency($name);
			$grab = count($parameters = $dependency->getParameterList()) - count($parameterList);
			$dependencyList = [];
			foreach ($parameters as $parameter) {
				/** @noinspection NotOptimalIfConditionsInspection */
				if ($grab-- <= 0) {
					break;
				}
				$dependencyList[] = $this->factory($this->getFactory($class = (($class = $parameter->getClass()) ? $class->getName() : $parameter->getName())), $class);
			}
			return $this->inject($factory->execute(array_merge($dependencyList, $parameterList), $name), $factory);
		}
	}
