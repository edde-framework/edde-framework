<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Container\LazyInjectTrait;

	trait SessionTrait {
		use LazyInjectTrait;

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
			$this->session = $this->sessionManager->getSession(static::class);
		}

		protected function lazyList(): array {
			return [
				'session' => function () {
					return $this->sessionManager->getSession(static::class);
				},
			];
		}
	}
