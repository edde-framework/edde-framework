<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Home\HomeView;
	use Edde\Common\Html\TemplateViewControl;
	use Edde\Common\Session\SessionTrait;

	class LoginView extends TemplateViewControl {
		use SessionTrait;

		public function handleOnLogin(LoginCrate $loginCrate) {
			$this->redirect([
				HomeView::class,
				'action-home',
			]);
		}
	}
