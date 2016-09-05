<?php
	declare(strict_types = 1);

	use Edde\Common\Control\AbstractControl;

	class TestControl extends AbstractControl {
		public function someMethod($boo, $foo) {
			return $foo . $boo;
		}

		public function __call($name, $a) {
			if ($name === 'dummy') {
				return 'dumyyyy';
			}
			return parent::__call($name, $a);
		}
	}
