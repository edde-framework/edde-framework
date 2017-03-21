<?php
	declare(strict_types=1);

	namespace Edde\App\Index;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;

	class IndexView extends AbstractControl {
		use LazyTemplateManagerTrait;
		use LazyRequestTrait;

		public function actionIndex() {
			$this->templateManager->template('layout', $this, null, $this);
		}

		public function actionFoo() {
		}

		public function actionBar() {
			echo 'bar';
		}

		public function getAction() {
			return StringUtils::recamel($this->request->getAction());
		}
	}
