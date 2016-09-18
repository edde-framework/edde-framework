<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Usable\IUsable;

	interface IIdentityManager extends IUsable {
		/**
		 * return identity crate by the given identifier (can be guid, login, ...)
		 *
		 * @param string $identity
		 *
		 * @return ICrate
		 */
		public function getIdentityCrate(string $identity): ICrate;

		/**
		 * return current identity build from session
		 *
		 * @return IIdentity
		 */
		public function identity(): IIdentity;

		/**
		 * update session data with current identity
		 *
		 * @return IIdentityManager
		 */
		public function update(): IIdentityManager;
	}
