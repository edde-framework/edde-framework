<?php
	use Edde\Common\Html\Document\DocumentControl;

	class TestDocument extends DocumentControl {
		public function switchMe() {
			return 'bar';
		}

		public function getItemList() {
			return [
				'item',
			];
		}
	}
