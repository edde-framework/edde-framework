<?php
	namespace Edde\Ext\Resource;

	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crypt\Crypt;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\Resource\FileStorage;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Url\Url;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;
	use phpunit\framework\TestCase;

	class StyleSheetResourceTest extends TestCase {
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		public function setUp() {
			$cacheFactory = $cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage());
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$container = new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory), $cacheFactory);
			$crateFactory = new CrateFactory($container);
			$this->storage = new DatabaseStorage($container, new SqliteDriver('sqlite:' . $this->getDatabaseFileName()), $cacheFactory);
			$this->schemaManager = new SchemaManager();
			$this->schemaManager->addSchema(new ResourceSchema());
			$this->upgradeManager = new UpgradeManager();
			$this->resourceManager = new ResourceManager($crateFactory, $this->schemaManager, $this->storage, new FilesystemScanner(__DIR__ . '/assets'), new Crypt());
			$factoryManager->registerFactory(ResourceStorable::class, FactoryFactory::create(ResourceStorable::class, [
				$this->resourceManager,
				'createResourceStorable',
			], false));
			$this->upgradeManager->registerUpgrade(new InitialStorageUpgrade($this->storage, $this->schemaManager, '1.0'));
			$this->upgradeManager->upgrade();
			$this->resourceManager->update();
		}

		protected function getDatabaseFileName() {
			return __DIR__ . '/temp/resource-test-' . sha1(microtime() . mt_rand(0, 99999)) . '.sqlite';
		}

		public function testCommon() {
			$styleSheetResource = new StyleSheetResource(new FileStorage($this->resourceManager, __DIR__, __DIR__ . '/public'), $this->resourceManager);
			$styleSheetResource->addStryleSheet(new Resource(Url::factory('file', __DIR__ . '/assets/css/font-awesome.css')));
			$styleSheetResource->addStryleSheet(new Resource(Url::factory('file', __DIR__ . '/assets/css/font-awesome.min.css')));
			$styleSheetResource->addStryleSheet(new Resource(Url::factory('file', __DIR__ . '/assets/css/simple-css.css')));
			$styleSheetResource->compile();
		}
	}
