<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Usable\AbstractUsable;

	class Authenticator extends AbstractUsable implements IAuthenticator {
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator {
			return $this;
		}

		protected function prepare() {
		}
	}
