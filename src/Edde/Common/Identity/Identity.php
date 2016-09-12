<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Identity\IdentityException;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Usable\AbstractUsable;

	class Identity extends AbstractUsable implements IIdentity {
		/**
		 * @var ICrate
		 */
		protected $identity;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $authenticated;
		/**
		 * @var array
		 */
		protected $metaList = [];

		public function __construct() {
			$this->name = 'unknown';
			$this->authenticated = false;
		}

		public function getMeta(string $name, $default = null) {
			return $this->metaList[$name] ?? ($default && is_callable($default) ? call_user_func($default) : $default);
		}

		public function getMetaList(): array {
			return $this->metaList;
		}

		public function setMetaList(array $metaList): IIdentity {
			$this->metaList = $metaList;
			return $this;
		}

		public function hasIdentity(): bool {
			return $this->identity !== null;
		}

		public function getIdentity(): ICrate {
			if ($this->identity === null) {
				throw new IdentityException(sprintf('Identity [%s] has no additional data.', $this->name));
			}
			return $this->identity;
		}

		public function setIdentity(ICrate $identity = null): IIdentity {
			$this->identity = $identity;
			return $this;
		}

		public function getName(): string {
			return $this->name;
		}

		public function setName(string $name): IIdentity {
			$this->name = $name;
			return $this;
		}

		public function isAuthenticated(): bool {
			return $this->authenticated;
		}

		public function setAuthenticated(bool $authenticated): IIdentity {
			$this->authenticated = $authenticated;
			return $this;
		}

		protected function prepare() {
		}
	}
