<?php
	declare(strict_types=1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Container\Container;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Config\AbstractConfigurator;

	class UpgradeManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IUpgradeManager $instance
		 */
		public function configure($instance) {
			$instance->registerUpgrade($this->container->create(InitialStorageUpgrade::class));
		}
	}
