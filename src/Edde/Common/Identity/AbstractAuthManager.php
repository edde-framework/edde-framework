<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractAuthManager extends AbstractUsable implements IAuthManager {
		use LazyInjectTrait;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}
	}
