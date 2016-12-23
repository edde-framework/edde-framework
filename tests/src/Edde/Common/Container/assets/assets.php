<?php
	declare(strict_types = 1);

	use Edde\Api\Container\IConfigHandler;
	use Edde\Common\AbstractObject;

	interface ISomething {
		public function registerSomeething(string $something);
	}

	class FirstSomethingSetup implements IConfigHandler {
		/**
		 * @param ISomething $instance
		 *
		 * @return IConfigHandler
		 */
		public function setup($instance): IConfigHandler {
			$instance->registerSomeething('foo');
			return $this;
		}
	}

	class AnotherSomethingSetup implements IConfigHandler {
		/**
		 * @param ISomething $instance
		 *
		 * @return IConfigHandler
		 */
		public function setup($instance): IConfigHandler {
			$instance->registerSomeething('bar');
			return $this;
		}
	}

	class Something extends AbstractObject implements ISomething {
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
			$this->somethingList = $something;
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
		 * @param AnotherSomething $anotherSomething
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
