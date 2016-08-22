<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Upgrade\AbstractUpgrade;

	class InitialIdentityUpgrade extends AbstractUpgrade {
		use LazyInjectTrait;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var IIdentityManager
		 */
		protected $identityManager;

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function lazyIdentityManager(IIdentityManager $identityManager) {
			$this->identityManager = $identityManager;
		}

		protected function onUpgrade() {
			$this->storage->start();
			try {
				$this->identityManager->createIdentity([
					'name' => 'root',
					'login' => 'root',
					'hash' => 'foobar',
				]);
				$this->storage->commit();
			} catch (\Exception $e) {
				$this->storage->rollback();
				throw $e;
			}
		}

		protected function prepare() {
		}
	}
