<?php
	declare(strict_types = 1);

	namespace App\Login;

	use Edde\Ext\Html\EddeViewControl;

	class LoginView extends EddeViewControl {
		public function actionLogin() {
			$this->setTitle('Login');
			$this->addStyleSheet(__DIR__ . '/../assets/css/kube.css');
			$this->template(__DIR__ . '/template/action-login.xml');
			$this->send();
		}

		public function handleOnLogin(LoginCrate $loginCrate) {
		}
	}