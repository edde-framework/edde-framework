<?php
	declare(strict_types = 1);

	namespace App\Login;

	use Edde\Ext\Html\EddeViewControl;

	class LoginView extends EddeViewControl {
		public function actionLogin() {
			$this->template();
			$this->send();
		}

		public function handleOnLogin(LoginCrate $loginCrate) {
		}
	}
