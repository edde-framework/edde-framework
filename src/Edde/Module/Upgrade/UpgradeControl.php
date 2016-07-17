<?php
	namespace Edde\Module\Upgrade;

	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Control\AbstractControl;

	class UpgradeControl extends AbstractControl {
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;

		final public function injectUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}

		public function actionUpgrade($version = null) {
			$upgrade = $this->upgradeManager->upgradeTo($version);
			printf("Upgraded to [%s]\n", $upgrade->getVersion());
		}

		protected function onPrepare() {
		}
	}
