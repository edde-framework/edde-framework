<?php
	namespace App\Login;

	use Edde\Common\Control\Html\HtmlControl;

	class LoginControl extends HtmlControl {
		public function actionLogin() {
			$this->setTitle('Login');
			$this->send();
		}

		protected function onPrepare() {
		}
	}
