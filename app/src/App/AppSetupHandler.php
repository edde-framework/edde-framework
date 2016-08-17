<?php
	declare(strict_types = 1);

	namespace App;

	use App\Login\LoginView;
	use App\Upgrade\InitialUpgrade;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;

	class AppSetupHandler extends DefaultSetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory, array_merge([
				LoginView::class,
			], $factoryList))
				->onSetup(ISchemaFactory::class, function (ICacheFactory $cacheFactory, IRootDirectory $rootDirectory, ISchemaFactory $schemaFactory) {
					$cache = $cacheFactory->factory(__DIR__);
					if (($schemaList = $cache->load('schema-list')) === null) {
						$schemaList = [];
						foreach ($rootDirectory->directory('src') as $file) {
							if (strpos($path = $file->getPath(), '-schema.json') === false) {
								continue;
							}
							$schemaList[] = $path;
						}
						$cache->save('schema-list', $schemaList);
					}
					foreach ($schemaList as $schema) {
						$schemaFactory->load($schema);
					}
				})
				->onSetup(IUpgradeManager::class, function (IContainer $container, IUpgradeManager $upgradeManager) {
					$upgradeManager->registerUpgrade($container->create(InitialStorageUpgrade::class, '0.0'));
					$upgradeManager->registerUpgrade($container->create(InitialUpgrade::class, '1.0'));
				});
		}
	}
