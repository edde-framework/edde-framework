<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Usable\AbstractUsable;

	class Authenticator extends AbstractUsable implements IAuthenticator {
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
