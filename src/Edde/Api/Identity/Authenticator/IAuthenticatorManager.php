<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity\Authenticator;

	use Edde\Api\Identity\IAuthManager;
	use Edde\Api\Identity\IIdentity;

	interface IAuthenticatorManager extends IAuthManager {
		/**
		 * @param IAuthenticator $authenticator
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager;

		/**
		 * try to use named authenticator for authenticate the given identity
		 *
		 * @param string $name
		 * @param IIdentity|null $identity
		 * @param array ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function authenticate(string $name, IIdentity $identity = null, ...$credentials): IAuthenticatorManager;
	}
