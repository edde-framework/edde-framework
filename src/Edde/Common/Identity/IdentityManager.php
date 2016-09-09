<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Identity\IdentityException;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Session\SessionTrait;
	use Edde\Common\Usable\AbstractUsable;

	class IdentityManager extends AbstractUsable implements IIdentityManager {
		use SessionTrait;
		/**
		 * @var IStorage
		 */
		protected $storage;

		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function getIdentityCrate(string $identity): ICrate {
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->all()
				->from()
				->source(IdentityStorable::class)
				->where()
				->eq()
				->property('guid')
				->parameter($identity)
				->or()
				->eq()
				->property('login')
				->parameter($identity);
			try {
				return $this->storage->load(IdentityStorable::class, $selectQuery);
			} catch (StorageException $e) {
				throw new IdentityException(sprintf('Unknown identity [%s].', $identity), 0, $e);
			}
		}

		public function identity(): IIdentity {
			if ($this->identity === null) {
				$this->identity = $this->session->get('identity', new Identity());
			}
			return $this->identity;
		}

		protected function prepare() {
		}
	}