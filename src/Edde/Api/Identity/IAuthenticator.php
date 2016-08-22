<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Usable\IUsable;

	/**
	 * This implementation is resposnible for an identity authentification.
	 */
	interface IAuthenticator extends IUsable {
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
