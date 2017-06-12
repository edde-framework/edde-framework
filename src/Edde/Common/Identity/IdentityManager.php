<?php
	declare(strict_types=1);

	namespace Edde\Common\Identity;

	use Edde\Api\Acl\LazyAclTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Identity\IdentityException;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\Session\SessionTrait;
	use Edde\Common\Storage\AbstractRepository;

	class IdentityManager extends AbstractRepository implements IIdentityManager {
		use LazyContainerTrait;
		use LazyStorageTrait;
		use LazyAclTrait;
		use SessionTrait;
		const SESSION_IDENTITY = 'identity';
		/**
		 * @var IIdentity
		 */
		protected $identity;

		/**
		 * @inheritdoc
		 */
		public function createIdentity(): IIdentity {
			return $this->identity ?: $this->identity = $this->session()->get(self::SESSION_IDENTITY, $this->container->create(Identity::class)->setup());
		}

		/**
		 * @inheritdoc
		 */
		public function update(): IIdentityManager {
			$this->session()->set(self::SESSION_IDENTITY, $this->createIdentity());
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function reset(bool $hard = true): IIdentityManager {
			$this->session()->set(self::SESSION_IDENTITY, null);
			$this->createIdentity();
			$this->identity->setAuthenticated(false);
			if ($this->acl !== $this->identity->getAcl()) {
				throw new IdentityException('Acl in identity differs from acl in container. This can lead to a security hole like a Hell!');
			}
			$this->acl->reset();
			if ($hard) {
				$this->identity->setMetaList([]);
				$this->identity->setName('');
			}
			return $this;
		}
	}
