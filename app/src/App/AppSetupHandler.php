<?php
	declare(strict_types = 1);

	namespace App;

	use App\Login\LoginView;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;

	class AppSetupHandler extends DefaultSetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory, array_merge([
				LoginView::class,
			], $factoryList))
				->onSetup(ISchemaFactory::class, function (ISchemaFactory $schemaFactory) {
					$schemaFactory->load(__DIR__ . '/Login/schema/login-schema.json');
				})
				->onSetup(IUpgradeManager::class, function (IContainer $container, IUpgradeManager $upgradeManager) {
					$upgradeManager->registerUpgrade($container->create(InitialStorageUpgrade::class, '1.0'));
				});
		}
	}
