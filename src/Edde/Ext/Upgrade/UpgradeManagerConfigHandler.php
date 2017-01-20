<?php
	declare(strict_types=1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\AbstractConfigHandler;

	class UpgradeManagerConfigHandler extends AbstractConfigHandler {
		use LazyContainerTrait;

		/**
		 * @param IUpgradeManager $instance
		 */
		public function config($instance) {
			$instance->registerUpgrade($this->container->create(InitialStorageUpgrade::class));
		}
	}
