<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;

	trait SessionTrait {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var ISession
		 */
		protected $session;

		public function lazySessionManager(ISessionManager $sessionManager) {
			$this->sessionManager = $sessionManager;
		}

		public function session() {
			$this->objectProperty('session', function () {
				return $this->sessionManager->getSession(static::class);
			});
		}
	}
