<?php
	declare(strict_types = 1);

	namespace App\Login;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Identity\AuthenticatorException;
	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Identity\AbstractAuthenticator;

	/**
	 * Authenticates against database table.
	 */
	class SimpleAuthenticator extends AbstractAuthenticator {
		use LazyInjectTrait;
		/**
		 * @var IIdentityManager
		 */
		protected $identityManager;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function lazyIdentityManager(IIdentityManager $identityManager) {
			$this->identityManager = $identityManager;
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			if (count($credentials) !== 2) {
				throw new AuthenticatorException(sprintf('Credentials must have exactly two values: login and password for [%s].', static::class));
			}
			list($login, $password) = $credentials;
			$crate = $this->identityManager->getIdentityCrate($login);
			if ($this->cryptEngine->verify($password, $crate->get('hash')) === false) {
				throw new AuthenticatorException(sprintf('Cannot authenticate [%s]: wrong password.', $crate->get('name')));
			}
			return $this;
		}

		protected function prepare() {
		}
	}
