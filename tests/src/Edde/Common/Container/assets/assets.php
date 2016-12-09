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
