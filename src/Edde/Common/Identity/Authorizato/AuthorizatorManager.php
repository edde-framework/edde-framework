<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity\Authorizato;

	use Edde\Api\Identity\Authorizator\AuthorizatorException;
	use Edde\Api\Identity\Authorizator\IAuthorizator;
	use Edde\Api\Identity\Authorizator\IAuthorizatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Identity\AbstractAuthManager;

	class AuthorizatorManager extends AbstractAuthManager implements IAuthorizatorManager {
		/**
		 * @var IAuthorizator[]
		 */
		protected $authorizatorList = [];

		public function registerAuthorizator(IAuthorizator $authorizator): IAuthorizatorManager {
			$this->authorizatorList[$authorizator->getName()] = $authorizator;
			return $this;
		}

		public function authorize(string $name, IIdentity $identity = null): IAuthorizatorManager {
			$identity ?: $this->identity;
			if (isset($this->authorizatorList[$name]) === false) {
				throw new AuthorizatorException(sprintf('Cannot authorize identity [%s] by an unknown authorizator [%s]; did you registered it before?', $identity->getName(), $name));
			}
			$this->authorizatorList[$name]->authorize($identity);
			return $this;
		}

		protected function prepare() {
		}
	}
