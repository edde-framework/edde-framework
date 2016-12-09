<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Container\AbstractFactory;

	/**
	 * Proxy to callback; factory will be called and then method in the result of factory.
	 */
	class CallbackProxyFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $factory;
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;

		/**
		 * @param string $name
		 * @param string $factory
		 * @param string $method
		 * @param IFactoryManager $factoryManager
		 */
		public function __construct($name, $factory, $method, IFactoryManager $factoryManager) {
			$this->name = $name;
			$this->factory = $factory;
			$this->method = $method;
			$this->factoryManager = $factoryManager;
		}

		public function canHandle($dependency): bool {
			return is_string($dependency) && $this->name === $dependency;
		}

		public function dependency($dependency): IDependency {
			return $this->factoryManager->getFactory($this->factory)
				->dependency($this->factory);
		}

		public function getFactory(): IFactory {
			return $this->factoryManager->getFactory($this->factory);
		}
	}
