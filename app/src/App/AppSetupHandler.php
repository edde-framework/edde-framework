<?php
	declare(strict_types = 1);

	namespace App;

	use App\Upgrade\InitialUpgrade;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Link\ControlLinkGenerator;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Edde\Ext\Upgrade\InitialIdentityUpgrade;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;

	class AppSetupHandler extends DefaultSetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory, array_merge([], $factoryList))
				->onSetup(ISchemaFactory::class, function (ICacheFactory $cacheFactory, IRootDirectory $rootDirectory, ISchemaFactory $schemaFactory) {
					$cache = $cacheFactory->factory(__DIR__);
					if (($schemaList = $cache->load('schema-list')) === null) {
						$schemaList = [];
						foreach ($rootDirectory->parent() as $file) {
							if (strpos($path = $file->getPath(), '-schema.json') === false && strpos($path, '-schema.php') === false) {
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
					$upgradeManager->registerUpgrade($container->create(InitialIdentityUpgrade::class, '0.1'));
					$upgradeManager->registerUpgrade($container->create(InitialUpgrade::class, '1.0'));
				})
				->onSetup(ILinkFactory::class, function (IContainer $container, ILinkFactory $linkFactory) {
					$linkFactory->registerLinkGenerator($container->create(ControlLinkGenerator::class));
				});
		}
	}
