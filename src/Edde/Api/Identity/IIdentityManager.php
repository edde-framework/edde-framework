<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	/**
	 * Formal shorthand for authorization/authentification.
	 */
	interface IIdentityManager extends IAuthenticator, IAuthorizator {
		/**
		 * try to build identity by default set of authenticator/authorizator
		 *
		 * @param array ...$credentials
		 *
		 * @return IIdentity
		 */
		public function identity(...$credentials): IIdentity;
	}
