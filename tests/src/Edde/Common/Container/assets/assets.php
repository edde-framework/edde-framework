<?php
	declare(strict_types = 1);

	use Edde\Api\Container\IConfigHandler;
	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\AbstractConfigHandler;
	use Edde\Common\Container\ConfigurableTrait;

	interface ISomething {
		public function registerSomeething(string $something);
	}

	class FirstSomethingSetup extends AbstractConfigHandler implements IConfigHandler {
		/**
		 * @param ISomething $instance
		 */
		public function config($instance) {
			$instance->registerSomeething('foo');
		}

		public function getBoo() {
			return 'boo';
		}
	}

	class AnotherSomethingSetup extends AbstractConfigHandler implements IConfigHandler {
		use LazyContainerTrait;

		/**
		 * @param ISomething $instance
		 */
		public function config($instance) {
			$instance->registerSomeething('bar');
			$instance->registerSomeething($this->container->create(FirstSomethingSetup::class)
				->getBoo());
		}

		public function wakeup(...$parameterList) {
			list($this->container) = $parameterList;
		}
	}

	class Something extends AbstractObject implements ISomething, IConfigurable {
		use ConfigurableTrait;

		public $someParameter;
		public $anotherSomething;
		public $injectedSomething;
		public $lazySomething;
		public $anotherAnotherSomething;
		public $somethingList = [];

		public function __construct($someParameter, AnotherSomething $anotherSomething) {
			$this->someParameter = $someParameter;
			$this->anotherSomething = $anotherSomething;
		}

		public function injectSomething(InjectedSomething $injectedSomething) {
		}

		public function lazySomething(LazySomething $lazySomething, AnotherAnotherSomething $anotherAnotherSomething) {
		}

		public function registerSomeething(string $something) {
			$this->somethingList[] = $something;
			return $this;
		}
	}

	class AnotherSomething extends AbstractObject {
	}

	class InjectedSomething extends AbstractObject {
	}

	class LazySomething extends AbstractObject {
	}

	class AnotherAnotherSomething extends AbstractObject {
	}

	class ThisIsCleverManager extends AbstractObject {
		/**
		 * @var AnotherSomething
		 */
		protected $anotherSomething;
		/**
		 * @var InjectedSomething
		 */
		protected $injectedSomething;

		/**
		 * @param AnotherSomething  $anotherSomething
		 * @param InjectedSomething $injectedSomething
		 */
		public function __construct(AnotherSomething $anotherSomething, InjectedSomething $injectedSomething) {
			$this->anotherSomething = $anotherSomething;
			$this->injectedSomething = $injectedSomething;
		}

		public function createCleverProduct(): ThisIsProductOfCleverManager {
			return new ThisIsProductOfCleverManager();
		}
	}

	class ThisIsProductOfCleverManager extends AbstractObject {
	}
