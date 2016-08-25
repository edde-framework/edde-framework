<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Home\HomeView;
	use Edde\Common\Session\SessionTrait;
	use Edde\Ext\Html\EddeViewControl;

	class LoginView extends EddeViewControl {
		use SessionTrait;

		public function handleOnLogin(LoginCrate $loginCrate) {
			$this->redirect([
				HomeView::class,
				'action-home',
			]);
		}
	}
