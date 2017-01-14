<?php
	declare(strict_types = 1);

	namespace Edde\App\Index;

	use Edde\Common\Html\ViewControl;

	class IndexView extends ViewControl {
		public function actionFoo() {
			$this->template();
			$this->response();
		}

		public function actionBar() {
			echo 'bar';
		}
	}
