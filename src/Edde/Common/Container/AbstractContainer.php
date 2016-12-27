<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Ext\Container\CallbackFactory;

	abstract class AbstractContainer extends AbstractObject implements IContainer {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var string[]
		 */
		protected $configHandlerList = [];

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
		 */
		public function registerConfigHandlerList(array $configHandlerList): IContainer {
			$this->configHandlerList = $configHandlerList;
			return $this;
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
	}
