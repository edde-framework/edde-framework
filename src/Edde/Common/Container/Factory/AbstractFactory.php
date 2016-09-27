<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $singleton;
		/**
		 * @var bool
		 */
		protected $cloneable;
		/**
		 * @var callable[]
		 */
		protected $onSetupList = [];
		protected $instance;
		protected $lock = false;

		/**
		 * Obsolete: Any computer you own.
		 *
		 * @param string $name
		 * @param bool $singleton
		 * @param bool $cloneable
		 */
		public function __construct(string $name, bool $singleton = true, bool $cloneable = false) {
			$this->name = $name;
			$this->singleton = $singleton;
			$this->cloneable = $cloneable;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function deffered(callable $callback) {
			$this->onSetupList[] = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $name): bool {
			return $this->name === $name;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function create(string $name, array $parameterList, IContainer $container) {
			if ($this->instance !== null) {
				if ($this->isCloneable()) {
					return clone $this->instance;
				}
				if ($this->isSingleton()) {
					return $this->instance;
				}
			}
			if ($this->lock) {
				throw new FactoryException(sprintf("Factory [%s] is locked; isn't there some circular dependency?", $this->name));
			}
			try {
				$this->lock = true;
				$container->inject($this->instance = $this->factory($name, $parameterList, $container));
				$this->setup($this->instance, $container);
				$this->lock = false;
				return $this->instance;
			} catch (\Exception $e) {
				$this->lock = false;
				/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
				throw $e;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function isCloneable(): bool {
			return $this->cloneable;
		}

		/**
		 * @inheritdoc
		 */
		public function setCloneable(bool $cloneable) {
			$this->cloneable = $cloneable;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isSingleton(): bool {
			return $this->singleton;
		}

		/**
		 * @inheritdoc
		 */
		public function setSingleton(bool $singleton) {
			$this->singleton = $singleton;
			return $this;
		}

		/**
		 * execute factory method with all required parameters already provided
		 *
		 * @param string $name
		 * @param array $parameterList
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		abstract public function factory(string $name, array $parameterList, IContainer $container);

		/**
		 * execute setup callbacks on registered callbacks
		 *
		 * @param mixed $instance
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		protected function setup($instance, IContainer $container) {
			foreach ($this->onSetupList as $callback) {
				$container->call($callback, $instance);
			}
			return $instance;
		}
	}
