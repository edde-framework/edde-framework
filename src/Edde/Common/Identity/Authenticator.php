<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;

	class Authenticator extends AbstractAuth implements IAuthenticator {
		use LazyInjectTrait;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}

		public function authenticate(IIdentity $identity = null, ...$credentials): IAuthenticator {
			$identity = $identity ?: $this->identity;
			return $this;
		}

		protected function prepare() {
		}
	}
