<?php
	declare(strict_types=1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Crate\LazyCrateFactoryTrait;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\Object;

	/**
	 * Upgrade Event Handler.
	 */
	class UpgradeHandler extends Object {
		use LazyStorageTrait;
		use LazySchemaManagerTrait;
		use LazyCrateFactoryTrait;

		public function eventOnUpgrade() {
			$upgradeStorable = $this->crateFactory->crate(, UpgradeStorable::class);
			$upgradeStorable->setStamp(microtime(true));
			$upgradeStorable->setVersion($onUpgradeEvent->getUpgrade()
				->getVersion());
			$this->storage->store($upgradeStorable);
		}
	}
