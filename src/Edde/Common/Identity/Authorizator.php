<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Usable\AbstractUsable;

	class Authorizator extends AbstractUsable implements IAuthorizator {
		use LazyInjectTrait;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}

		public function authorize(IIdentity $identity = null): IAuthorizator {
			$identity = $identity ?: $this->identity;
			return $this;
		}

		protected function prepare() {
		}
	}
