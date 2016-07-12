<?php
	namespace Edde\Common\Resource;

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
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Storage\StorableFactory;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;
	use phpunit\framework\TestCase;

	class ResourceManagerTest extends TestCase {
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
			@unlink($this->getDatabaseFileName());
			$cacheFactory = $cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage());
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$container = new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory), $cacheFactory);
			$crateFactory = new CrateFactory($container);
			$this->storage = new DatabaseStorage(new SqliteDriver('sqlite:' . $this->getDatabaseFileName()), $cacheFactory);
			$this->schemaManager = new SchemaManager();
			$this->schemaManager->addSchema(new ResourceSchema());
			$this->upgradeManager = new UpgradeManager();
			$storableFactory = new StorableFactory($container, $crateFactory);
			$this->resourceManager = new ResourceManager($storableFactory, $this->schemaManager, $this->storage, new FilesystemScanner(__DIR__ . '/assets'), new Crypt());
			$this->upgradeManager->registerUpgrade(new InitialStorageUpgrade($this->storage, $this->schemaManager, '1.0'));
			$this->upgradeManager->upgrade();
		}

		protected function getDatabaseFileName() {
			return __DIR__ . '/resource-test.sqlite';
		}

		public function testUpdate() {
			$this->resourceManager->update();
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->count('*', null, 'count')
				->from()
				->source(Resource::class);
			$row = null;
			foreach ($this->storage->execute($selectQuery) as $row) {
				break;
			}
			self::assertNotEmpty($row);
			self::assertEquals(3, $row['count']);
		}

		public function testSimpleQueries() {
			$this->resourceManager->update();
			$resource = $this->resourceManager->getResource($this->resourceManager->createResourceQuery()
				->nameLike('%.poo'));
		}
	}
