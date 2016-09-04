<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Home\HomeView;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\TemplateViewControl;
	use Edde\Common\Session\SessionTrait;

	class LoginView extends TemplateViewControl {
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
