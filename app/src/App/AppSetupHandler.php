<?php
	namespace App;

	use App\Login\LoginControl;
	use App\Login\LoginCrate;
	use App\Login\LoginCrateSchema;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Ext\Runtime\DefaultSetupHandler;

	class AppSetupHandler extends DefaultSetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory, array_merge([
				LoginControl::class,
				LoginCrate::class,
				LoginCrateSchema::class,
			], $factoryList))
				->onSetup(ISchemaManager::class, function (ISchemaManager $schemaManager) {
					$schemaManager->addSchema(new LoginCrateSchema());
				});
		}
	}
