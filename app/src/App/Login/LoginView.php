<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Home\HomeView;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Session\SessionTrait;
	use Edde\Ext\Html\EddeViewControl;

	class LoginView extends EddeViewControl {
		use SessionTrait;
		/**
		 * @var DivControl
		 */
		protected $login;

		public function handleOnLogin(LoginCrate $loginCrate) {
			$this->redirect([
				HomeView::class,
				'action-home',
			]);
		}

		public function handleOnShow() {
			$this->template(__DIR__ . '/template/action-login.xml');
			$this->login->dirty();
			$this->response();
		}
	}
