<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Upgrade\UpgradeException;
	use Edde\Common\Usable\AbstractUsable;

	class UpgradeManager extends AbstractUsable implements IUpgradeManager {
		/**
		 * @var IUpgrade[]
		 */
		protected $upgradeList = [];

		public function registerUpgrade(IUpgrade $upgrade, $force = false) {
			$version = $upgrade->getVersion();
			if ($force === false && isset($this->upgradeList[$version])) {
				throw new UpgradeException(sprintf('Cannot register upgrade [%s] with version [%s] - version is already present.', get_class($upgrade), $version));
			}
			$this->upgradeList[$upgrade->getVersion()] = $upgrade;
			return $this;
		}

		public function getUpgradeList() {
			$this->usse();
			return $this->upgradeList;
		}

		public function upgrade() {
			return $this->upgradeTo();
		}

		public function upgradeTo($version = null) {
			$this->usse();
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
			foreach ($this->upgradeList as $upgrade) {
				$upgrade->upgrade();
				if ($upgrade->getVersion() === $version) {
					break;
				}
			}
			if ($upgrade === null) {
				throw new UpgradeException(sprintf('No upgrades has been run for version [%s].', $version));
			}
			return $upgrade;
		}

		protected function prepare() {
		}
	}
