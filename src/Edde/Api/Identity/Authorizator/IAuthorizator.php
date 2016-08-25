<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity\Authorizator;

	use Edde\Api\Identity\IAuth;
	use Edde\Api\Identity\IIdentity;

	/**
	 * Implementation of an ACL mechanism; this should set roles to the given identity.
	 */
	interface IAuthorizator extends IAuth {
		/**
		 * update list of roles (ACL) of the given identity
		 *
		 * @param IIdentity $identity
		 *
		 * @return IAuthorizator
		 */
		public function authorize(IIdentity $identity): IAuthorizator;
	}
