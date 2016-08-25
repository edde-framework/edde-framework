<?php
	declare(strict_types = 1);

	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\Authorizator\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Identity\AbstractAuth;
	use Edde\Common\Session\Session;
	use Edde\Common\Usable\AbstractUsable;

	class TrustedAuthenticator extends AbstractAuth implements IAuthenticator {
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			$identity->setAuthenticated(true);
			return $this;
		}

		protected function prepare() {
		}
	}

	class InitialAuthenticator extends AbstractAuth implements IAuthenticator {
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			$identity->setName('whepee');
			return $this;
		}

		protected function prepare() {
		}
	}

	class SecondaryAuthenticator extends AbstractAuth implements IAuthenticator {
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			return $this;
		}

		protected function prepare() {
		}
	}

	class TrustedAuth extends AbstractAuth implements IAuthorizator {
		public function authorize(IIdentity $identity): IAuthorizator {
			return $this;
		}

		protected function prepare() {
		}
	}

	class DummySession extends AbstractUsable implements ISessionManager {
		protected $session = [];

		public function start(): ISessionManager {
			return $this;
		}

		public function isSession(): bool {
			return true;
		}

		public function getSession(string $name): ISession {
			return new Session($this, $name);
		}

		public function &session(string $name): array {
			$this->session[$name] = [];
			return $this->session[$name];
		}

		public function close(): ISessionManager {
			return $this;
		}

		protected function prepare() {
		}
	}
