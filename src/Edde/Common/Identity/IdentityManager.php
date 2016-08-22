<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Usable\AbstractUsable;

	class IdentityManager extends AbstractUsable implements IIdentityManager {
		use LazyInjectTrait;
		/**
		 * @var IAuthenticator
		 */
		protected $authenticator;
		/**
		 * @var IAuthorizator
		 */
		protected $authorizator;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyAuthenticator(IAuthenticator $authenticator) {
			$this->authenticator = $authenticator;
		}

		public function lazyAuthorizator(IAuthorizator $authorizator) {
			$this->authorizator = $authorizator;
		}

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}

		public function identity(...$credentials): IIdentity {
			$this->authenticate($this->identity, ...$credentials);
			$this->authorize($this->identity);
			return $this->identity;
		}

		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			$this->authenticator->authenticate($identity, ...$credentials);
			return $this;
		}

		public function authorize(IIdentity $identity): IAuthorizator {
			$this->authorizator->authorize($identity);
			return $this;
		}

		protected function prepare() {
		}
	}
