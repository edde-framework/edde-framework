<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity\Authenticator;

	use Edde\Api\Identity\IAuth;
	use Edde\Api\Identity\IIdentity;

	/**
	 * This implementation is resposnible for an identity authentification.
	 */
	interface IAuthenticator extends IAuth {
		/**
		 * authenticate a given identity or throw an exception
		 *
		 * @param IIdentity $identity if not specified, global one is used
		 * @param array ...$credentials
		 *
		 * @return IAuthenticator
		 */
		public function authenticate(IIdentity $identity = null, ...$credentials): IAuthenticator;
	}
