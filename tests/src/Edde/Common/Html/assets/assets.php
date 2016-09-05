<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\ViewControl;

	class MyLittleCuteView extends ViewControl {
		/**
		 * @var DivControl
		 */
		public $templateSnippet;

		public function myDivSnippet(DivControl $divControl) {
			$divControl->setText('foo');
			$divControl->dirty();
		}

		public function myDummySnippet(DivControl $divControl) {
		}

		public function templateSnippet(DivControl $divControl) {
			$divControl->dirty();
		}
	}
