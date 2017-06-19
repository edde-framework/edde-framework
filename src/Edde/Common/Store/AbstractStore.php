<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Lock\LazyLockManagerTrait;
	use Edde\Api\Store\IStore;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractStore extends Object implements IStore {
		use LazyLockManagerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function lock(string $name = null, bool $block = true): IStore {
			$this->lockManager->lock($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function block(string $name = null, int $timeout = null): IStore {
			$this->lockManager->block($this->getLockName($name), $timeout);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name = null): IStore {
			$this->lockManager->unlock($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function kill(string $name = null): IStore {
			$this->lockManager->kill($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(string $name = null): bool {
			return $this->lockManager->isLocked($this->getLockName($name));
		}

		/**
		 * @inheritdoc
		 */
		public function pickup(string $name, int $timeout = null) {
			$item = $this->get($name);
			$this->block($name, $timeout);
			$this->remove($name);
			$this->unlock($name);
			return $item;
		}

		protected function getLockName(string $name = null): string {
			return static::class . ($name ? '/' . $name : '');
		}
	}
