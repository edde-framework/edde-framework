<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;

	class InstanceFactory extends AbstractFactory {
		/**
		 * @param string $name
		 * @param object $instance
		 */
		public function __construct($name, $instance) {
			parent::__construct($name);
			$this->instance = $instance;
		}

		public function getParameterList() {
			/**
			 * instance is already living, so no parameters are needed
			 */
			return [];
		}

		public function onSetup(callable $callback) {
			throw new FactoryException(sprintf('Cannot register onSetup handler on [%s]; setup handlers are not supported by this factory.', self::class));
		}

		public function create($name, array $parameterList, IContainer $container) {
			return $this->instance;
		}

		public function factory(array $parameterList, IContainer $container) {
			throw new FactoryException('Something went wrong. God will kill one cute kitten and The Deep Evil of The Most Evilest Hell will eat it!');
		}
	}
