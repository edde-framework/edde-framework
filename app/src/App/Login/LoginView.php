<?php
	declare(strict_types = 1);

	namespace App\Login;

	use App\Home\HomeView;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Ext\Html\EddeViewControl;

	class LoginView extends EddeViewControl {
		use LazyInjectTrait;
		/**
		 * @var IIdentity
		 */
		protected $identity;
		/**
		 * @var IIdentityManager
		 */
		protected $identityManager;

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}

		public function lazyIdentityManager(IIdentityManager $identityManager) {
			$this->identityManager = $identityManager;
		}

		public function actionLogin() {
			if ($this->identity->isAuthenticated()) {
				$this->redirect([
					HomeView::class,
					'action-home',
				]);
				return;
			}
			$this->template();
			$this->send();
		}

		public function handleOnLogin(LoginCrate $loginCrate) {
			$this->identityManager->auth($loginCrate->getLogin(), $loginCrate->getPassword());
			$this->redirect([
				HomeView::class,
				'action-home',
			]);
		}
	}
