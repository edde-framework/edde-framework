<?php
	declare(strict_types = 1);

	namespace App;

	use App\Login\LoginControl;
	use App\Login\LoginCrateSchema;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;

	class AppSetupHandler extends DefaultSetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory, array_merge([
				LoginControl::class,
				LoginCrateSchema::class,
			], $factoryList))
				->onSetup(ISchemaManager::class, function (ISchemaManager $schemaManager) {
					$schemaManager->addSchema(new LoginCrateSchema());
				})
				->onSetup(IUpgradeManager::class, function (IContainer $container, IUpgradeManager $upgradeManager) {
					$upgradeManager->registerUpgrade($container->create(InitialStorageUpgrade::class, '1.0'));
				});
		}
	}
