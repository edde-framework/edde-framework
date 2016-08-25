<?php
	declare(strict_types = 1);

	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Identity\AbstractAuth;

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
			$login = reset($credentials);
			$password = end($credentials);
			if ($login === 'foo' && $password === 'bar') {
				$identity->setName('whepee');
			}
			return $this;
		}

		protected function prepare() {
		}
	}

	class SecondaryAuthenticator extends AbstractAuth implements IAuthenticator {
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			$login = reset($credentials);
			$password = end($credentials);
			if ($login === 'boo' && $password === 'poo') {
				$identity->setAuthenticated(true);
			}
			return $this;
		}

		protected function prepare() {
		}
	}
