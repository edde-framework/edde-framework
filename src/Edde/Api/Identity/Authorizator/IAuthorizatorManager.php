<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity\Authorizator;

	use Edde\Api\Identity\IAuthManager;
	use Edde\Api\Identity\IIdentity;

	interface IAuthorizatorManager extends IAuthManager {
		/**
		 * @param IAuthorizator $authorizator
		 *
		 * @return IAuthorizatorManager
		 */
		public function registerAuthorizator(IAuthorizator $authorizator): IAuthorizatorManager;

		/**
		 * @param IIdentity|null $identity
		 *
		 * @return IAuthorizatorManager
		 */
		public function authorize(IIdentity $identity = null): IAuthorizatorManager;
	}
