<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Tag\DivControl;

	class SomeCoolControl extends DivControl {
		public function loopFromRoot() {
			return [];
		}

		public function loopOverLoopFromRoot() {
			for ($i = 0; $i < 3; $i++) {
				yield 'upper-loop-key' . $i => (function () {
					for ($i = 0; $i < 4; $i++) {
						yield 'inner-loop-key-' . $i => 'inner-loop-value-' . $i;
					}
				})();
			}
		}

		public function rootMethodCall() {
			return 'ou-yay!';
		}

		public function currentMethodCall() {
			return 'yahoo!';
		}
	}

	class AnotherCoolControl extends DivControl {
		public function loopFromLocalControl() {
			for ($i = 0; $i < 3; $i++) {
				yield 'foobarpoo-' . $i => new class($i) {
					protected $i;

					public function __construct($i) {
						$this->i = $i;
					}

					public function getClass() {
						return 'clazz-[' . $this->i . ']-here';
					}
				};
			}
		}
	}
