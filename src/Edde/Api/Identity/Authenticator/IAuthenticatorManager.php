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
		 * optionaly define authentication flow (support for chained login methods - credentials + sms, ...)
		 *
		 * @param string $initial
		 * @param string[] ...$authenticatorList
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerFlow(string $initial, string ...$authenticatorList): IAuthenticatorManager;

		/**
		 * execute an authentication flow; if the flow fails, $flow will be used as initial authenticator
		 *
		 * @param string $flow
		 * @param IIdentity|null $identity
		 * @param array ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function flow(string $flow, IIdentity $identity = null, ...$credentials): IAuthenticatorManager;

		/**
		 * handy method to check if there is opened flow
		 *
		 * @return bool
		 */
		public function hasFlow(): bool;

		/**
		 * return upcoming flow or ampty array when there is no more flow
		 *
		 * @return string[]
		 */
		public function getFlow(): array;

		/**
		 * return name of a current authenticator or null when there is no flow
		 *
		 * @return string
		 */
		public function getCurrentFlow();

		/**
		 * authenticator manager should keep current flow state even when exception; this should restart the selected flow
		 *
		 * @param string $flow
		 *
		 * @return IAuthenticatorManager
		 */
		public function reset(string $flow): IAuthenticatorManager;

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
