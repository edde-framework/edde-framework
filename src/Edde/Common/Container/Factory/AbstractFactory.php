<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

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
		 * @param string $name
		 * @param bool $singleton
		 * @param bool $cloneable
		 */
		public function __construct($name, $singleton = true, $cloneable = false) {
			$this->name = $name;
			$this->singleton = $singleton;
			$this->cloneable = $cloneable;
		}

		public function getName() {
			return $this->name;
		}

		public function onSetup(callable $callback) {
			$this->onSetupList[] = $callback;
			return $this;
		}

		public function create($name, array $parameterList, IContainer $container) {
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
				$container->inject($this->instance = $this->factory($parameterList, $container));
				$this->setup($this->instance, $container);
				$this->lock = false;
				return $this->instance;
			} catch (\Exception $e) {
				$this->lock = false;
				throw $e;
			}
		}

		public function isCloneable(): bool {
			return $this->cloneable;
		}

		public function setCloneable(bool $cloneable) {
			$this->cloneable = $cloneable;
			return $this;
		}

		public function isSingleton(): bool {
			return $this->singleton;
		}

		public function setSingleton(bool $singleton) {
			$this->singleton = $singleton;
			return $this;
		}

		abstract public function factory(array $parameterList, IContainer $container);

		protected function setup($instance, IContainer $container) {
			foreach ($this->onSetupList as $callback) {
				$container->call($callback, $instance);
			}
			return $instance;
		}
	}
