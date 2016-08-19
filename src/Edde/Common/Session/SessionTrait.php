<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Session\SessionException;

	trait SessionTrait {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var ISession
		 */
		protected $session;

		public function injectSessionManager(ISessionManager $sessionManager) {
			$this->sessionManager = $sessionManager;
		}

		/**
		 * prepare session section for this class
		 *
		 * @throws SessionException
		 */
		protected function session() {
			if ($this->sessionManager === null) {
				throw new SessionException(sprintf('Session manager has not been injected into class [%s]; cannot use session.', static::class));
			}
			$this->session = $this->sessionManager->getSession(static::class);
		}
	}
