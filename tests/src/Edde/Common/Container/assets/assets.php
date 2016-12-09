<?php
	declare(strict_types = 1);

	class Something {
		protected $lazySomething;
		protected $anotherAnotherSomething;

		public function __construct($someParameter, AnotherSomething $anotherSomething) {
		}

		public function injectSomething(InjectedSomething $injectedSomething) {
		}

		public function lazySomething(LazySomething $lazySomething, AnotherAnotherSomething $anotherAnotherSomething) {
		}
	}

	class AnotherSomething {
	}

	class InjectedSomething {
	}

	class LazySomething {
	}

	class AnotherAnotherSomething {
	}

	class ThisIsCleverManager {
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

		public function createCleverProduct() {
			return new ThisIsProductOfCleverManager();
		}
	}

	class ThisIsProductOfCleverManager {
	}
