<?php
	use Edde\Common\Html\Document\DocumentControl;

	class TestDocument extends DocumentControl {
		public function switchMe() {
			return 'bar';
		}
	}
