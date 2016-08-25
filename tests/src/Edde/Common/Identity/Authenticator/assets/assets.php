<?php
	declare(strict_types = 1);

	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\Authorizator\IAuthorizator;
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
