<?php
	declare(strict_types = 1);

	namespace Edde\App\Index;

	use Edde\Common\Html\ViewControl;

	class IndexView extends ViewControl {
		public function actionFoo() {
			echo 'foo';
		}

		public function actionBar() {
			echo 'bar';
		}
	}
