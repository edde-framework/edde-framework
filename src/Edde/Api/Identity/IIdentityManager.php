<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	/**
	 * Formal shorthand for authorization/authentification.
	 */
	interface IIdentityManager extends IAuthenticator, IAuthorizator {
		/**
		 * create a new identity
		 *
		 * @param array $identity
		 *
		 * @return IIdentityManager
		 */
		public function createIdentity(array $identity): IIdentityManager;

		/**
		 * try to build identity by default set of authenticator/authorizator
		 *
		 * @param array ...$credentials
		 *
		 * @return IIdentity
		 */
		public function auth(...$credentials): IIdentity;
	}
