<?php
	declare(strict_types=1);

	namespace Edde\App\Index;

	use Edde\Common\Object\Object;

	class IndexView extends Object {
		public function actionIndex() {
			echo 'hello!';
		}
	}
