<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Tag\DivControl;

	class SomeCoolControl extends DivControl {
		public function loopFromRoot() {
			return [];
		}

		public function loopOverLoopFromRoot() {
			return [];
		}
	}
