<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\Authorizator\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;

	class Authorizator extends AbstractAuth implements IAuthorizator {
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
