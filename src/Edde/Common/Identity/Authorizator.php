<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Usable\AbstractUsable;

	class Authorizator extends AbstractUsable implements IAuthorizator {
		public function authorize(IIdentity $identity): IAuthorizator {
			return $this;
		}

		protected function prepare() {
		}
	}
