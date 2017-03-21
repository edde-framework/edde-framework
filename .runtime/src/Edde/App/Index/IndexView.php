<?php
	declare(strict_types=1);

	namespace Edde\App\Index;

	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Control\AbstractControl;

	class IndexView extends AbstractControl {
		use CacheTrait;

		public function actionIndex() {
			$cache = $this->cache();
			$cache->save('foo', true);
			echo 'hello there';
		}

		public function actionFoo() {
		}

		public function actionBar() {
			echo 'bar';
		}
	}
