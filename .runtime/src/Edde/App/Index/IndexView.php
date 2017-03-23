<?php
	declare(strict_types=1);

	namespace Edde\App\Index;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Template\TemplateResponse;

	class IndexView extends AbstractControl {
		use LazyTemplateManagerTrait;
		use LazyResponseManagerTrait;
		use LazyRequestTrait;

		public function actionIndex() {
			$this->responseManager->response(new TemplateResponse($template = $this->templateManager->template()));
			$template->template('layout', $this, null, $this);
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
