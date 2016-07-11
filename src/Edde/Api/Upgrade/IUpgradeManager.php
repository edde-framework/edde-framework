<?php
	namespace Edde\Api\Upgrade;

	/**
	 * This class is responsible for proper application upgrades.
	 */
	interface IUpgradeManager {
		/**
		 * register the given upgrade under it's version; exception should be thrown if version is already present
		 *
		 * @param IUpgrade $upgrade
		 * @param bool $force if true, upgrade is registered regardless of version
		 *
		 * @return $this
		 */
		public function registerUpgrade(IUpgrade $upgrade, $force = false);

		/**
		 * return current list of upgrades
		 *
		 * @return IUpgrade[]
		 */
		public function getUpgradeList();

		/**
		 * run upgrade; an implementation is responsible for proper upgrade execution (for example based on a version, ...)
		 *
		 * @return IUpgrade last run upgrade
		 *
		 * @throws UpgradeException
		 */
		public function upgrade();

		/**
		 * run upgrades to the given version; if the version is not found in the current upgrade list, exception should be thrown
		 *
		 * @param string|null $version
		 *
		 * @return IUpgrade last run upgrade
		 *
		 * @throws UpgradeException
		 */
		public function upgradeTo($version = null);
	}
