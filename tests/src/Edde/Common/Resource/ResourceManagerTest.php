<?php
	namespace Edde\Common\Resource;

	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crypt\Crypt;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Storage\StorableFactory;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use phpunit\framework\TestCase;

	class ResourceManagerTest extends TestCase {
		public function testUpdate() {
			$resourceManager = $this->createResourceManager();
			$resourceManager->update();
		}

		protected function createResourceManager() {
			$schemaManager = new SchemaManager();
			$schemaManager->addSchema(new ResourceSchema());
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			return new ResourceManager(new StorableFactory($container = new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage())), $cacheFactory), new CrateFactory($container)), $schemaManager, new DatabaseStorage(), new FilesystemScanner(__DIR__ . '/assets'), new Crypt());
		}

		public function testCommon() {
			$resourceManager = $this->createResourceManager();
		}
	}
