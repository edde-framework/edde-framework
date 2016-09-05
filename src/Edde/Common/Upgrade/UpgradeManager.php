<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Upgrade\UpgradeException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Usable\AbstractUsable;

	class UpgradeManager extends AbstractUsable implements IUpgradeManager {
		use LazyInjectTrait;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var IUpgrade[]
		 */
		protected $upgradeList = [];

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function registerUpgrade(IUpgrade $upgrade, $force = false) {
			$version = $upgrade->getVersion();
			if ($force === false && isset($this->upgradeList[$version])) {
				throw new UpgradeException(sprintf('Cannot register upgrade [%s] with version [%s] - version is already present.', get_class($upgrade), $version));
			}
			$this->upgradeList[$upgrade->getVersion()] = $upgrade;
			return $this;
		}

		public function getUpgradeList() {
			$this->use();
			return $this->upgradeList;
		}

		public function upgrade() {
			return $this->upgradeTo();
		}

		public function upgradeTo($version = null) {
			$this->use();
			if ($version === null) {
				end($this->upgradeList);
				$version = key($this->upgradeList);
			}
			if ($version === null) {
				throw new UpgradeException('Cannot run upgrade - there are no upgrades.');
			}
			if (isset($this->upgradeList[$version]) === false) {
				throw new UpgradeException(sprintf('Cannot run upgrade - unknown upgrade version [%s].', $version));
			}
			$upgrade = null;
			try {
				$this->storage->start();
				foreach ($this->upgradeList as $upgrade) {
					$upgrade->upgrade();
					if ($upgrade->getVersion() === $version) {
						break;
					}
				}
				$this->storage->commit();
			} catch (\Exception $e) {
				$this->storage->rollback();
				throw $e;
			}
			if ($upgrade === null) {
				throw new UpgradeException(sprintf('No upgrades has been run for version [%s].', $version));
			}
			return $upgrade;
		}

		protected function prepare() {
		}
	}
