<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Message\FlashControl;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\TemplateViewControl;
	use Edde\Common\Session\SessionTrait;

	class LoginView extends TemplateViewControl {
		use SessionTrait;
		/**
		 * @var IAuthenticatorManager
		 */
		protected $authenticatorManager;
		/**
		 * @var DivControl
		 */
		protected $login;
		/**
		 * @var FlashControl
		 */
		protected $flash;

		public function lazyAuthenticatorManager(IAuthenticatorManager $authenticatorManager) {
			$this->authenticatorManager = $authenticatorManager;
		}

		public function actionLogin() {
			$this->authenticatorManager->select(SimpleAuthenticator::class);
			$this->template();
			$this->response();
		}

		public function handleOnLogin(LoginCrate $loginCrate) {
			$this->snippet(__DIR__ . '/../template/layout.xml', 'flash');
			$this->flash->setText('foo');
			$this->flash->dirty();
//			$this->authenticatorManager->flow(SimpleAuthenticator::class, $loginCrate->getLogin(), $loginCrate->getPassword());
//			$this->redirect([
//				HomeView::class,
//				'action-home',
//			]);
			$this->response();
		}

		public function handleOnShow() {
			$this->snippet(__DIR__ . '/template/action-login.xml', 'login');
			$this->login->dirty();
			$this->response();
		}
	}
