<?php
	declare(strict_types=1);

	namespace Edde\App\Login;

	use Edde\Ext\Control\AbstractTemplateControl;

	class LoginView extends AbstractTemplateControl {
		public function actionHandleLogin() {
			$body = $this->getContent();
		}
	}
