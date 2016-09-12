<?php
	declare(strict_types = 1);

	namespace App\Upgrade;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Identity\IdentityStorable;
	use Edde\Common\Upgrade\AbstractUpgrade;

	class InitialUpgrade extends AbstractUpgrade {
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		protected function onUpgrade() {
			$identityStorable = $this->crateFactory->crate(IdentityStorable::class);
			$identityStorable->setName('root');
			$identityStorable->setLogin('root');
			$identityStorable->setHash($this->cryptEngine->password('aB1234'));
			$this->storage->store($identityStorable);
		}

		protected function prepare() {
		}
	}
