<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Store\IStore;
	use Edde\Api\Store\IStoreManager;
	use Edde\Common\Config\ConfigurableTrait;

	abstract class AbstractStoreManager extends AbstractStore implements IStoreManager {
		use ConfigurableTrait;
		/**
		 * @var IStore[]
		 */
		protected $storeList = [];
		/**
		 * @var IStore
		 */
		protected $current;

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IStore {
			$this->current->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return $this->current->has($name);
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			return $this->current->get($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IStore {
			$this->current->remove($name);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function drop(): IStore {
			$this->current->drop();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerStore(IStore $store, string $name = null): IStoreManager {
			$this->storeList[$name ?: get_class($store)] = $store;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function select(string $name): IStoreManager {
			if (isset($this->storeList[$name]) === false) {
				throw new UnknownStoreException(sprintf('Requested store [%s] which is not known in current store manager.' . ($this->isSetup() ? ' Manager has been set up.' : 'Manager has not been set up, try to call ::setup() method.'), $name));
			}
			$this->current = $this->storeList[$name];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->current->setup();
		}
	}
