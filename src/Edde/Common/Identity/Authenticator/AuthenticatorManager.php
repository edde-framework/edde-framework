<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity\Authenticator;

	use Edde\Api\Identity\Authenticator\AuthenticatorException;
	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\Authenticator\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Identity\AbstractAuthManager;

	class AuthenticatorManager extends AbstractAuthManager implements IAuthenticatorManager {
		/**
		 * @var IAuthenticator[]
		 */
		protected $authenticatorList = [];

		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager {
			$this->authenticatorList[$authenticator->getName()] = $authenticator;
			return $this;
		}

		public function authenticate(string $name, IIdentity $identity = null, ...$credentials): IAuthenticatorManager {
			$this->use();
			if (isset($this->authenticatorList[$name]) === false) {
				throw new AuthenticatorException(sprintf('Cannot authenticate identity by unknown authenticator [%s]; did you registered it before?', $name));
			}
			$this->authenticatorList[$name]->authenticate($identity ?: $this->identity, ...$credentials);
			return $this;
		}

		protected function prepare() {
		}
	}
