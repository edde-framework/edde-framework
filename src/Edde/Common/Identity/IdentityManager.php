<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Storage\IStorage;
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
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function lazyAuthenticator(IAuthenticator $authenticator) {
			$this->authenticator = $authenticator;
		}

		public function lazyAuthorizator(IAuthorizator $authorizator) {
			$this->authorizator = $authorizator;
		}

		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function createIdentity(array $identity): IIdentityManager {
			$this->storage->store($this->crateFactory->crate(IdentityStorable::class)
				->set('guid', $this->cryptEngine->guid())
				->put($identity));
			return $this;
		}

		public function auth(...$credentials): IIdentity {
			$this->authenticate($this->identity, ...$credentials);
			$this->authorize($this->identity);
			return $this->identity;
		}

		public function authenticate(IIdentity $identity = null, ...$credentials): IAuthenticator {
			$this->authenticator->authenticate($identity, ...$credentials);
			return $this;
		}

		public function authorize(IIdentity $identity = null): IAuthorizator {
			$this->authorizator->authorize($identity);
			return $this;
		}

		protected function prepare() {
		}
	}
