<?php
	namespace Edde\Common\Resource;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\Directory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;
	use phpunit\framework\TestCase;

	class ResourceIndexTest extends TestCase {
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
		 * @var IResourceIndex
		 */
		protected $resourceIndex;
		/**
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		public function setUp() {
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();
			$cacheFactory = $cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage());
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$container = new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory), $cacheFactory);
			$this->storage = new DatabaseStorage($container, $this->sqliteDriver = new SqliteDriver('sqlite:' . $this->getDatabaseFileName()), $cacheFactory);
			$this->schemaManager = new SchemaManager();
			$this->schemaManager->addSchema(new ResourceSchema());
			$this->upgradeManager = new UpgradeManager();
			$this->resourceIndex = new ResourceIndex($container, $this->schemaManager, $this->storage, new FilesystemScanner(new Directory(__DIR__ . '/assets')), new CryptEngine());
			$factoryManager->registerFactory(ResourceStorable::class, FactoryFactory::create(ResourceStorable::class, [
				$this->resourceIndex,
				'createResourceStorable',
			], false));
			$this->upgradeManager->registerUpgrade(new InitialStorageUpgrade($this->storage, $this->schemaManager, '1.0'));
			$this->upgradeManager->upgrade();
		}

		protected function getDatabaseFileName() {
			return $this->tempDirectory->getDirectory() . '/resource-test-' . sha1(microtime() . mt_rand(0, 99999)) . '.sqlite';
		}

		public function testUpdate() {
			$this->resourceIndex->update();
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->count('*', null, 'count')
				->from()
				->source(ResourceStorable::class);
			$row = null;
			/** @var $row array */
			foreach ($this->storage->execute($selectQuery) as $row) {
				break;
			}
			self::assertNotEmpty($row);
			self::assertEquals(3, $row['count']);
		}

		public function testSimpleQueries() {
			$this->resourceIndex->update();
			$resource = $this->resourceIndex->query()
				->nameLike('%.poo')
				->resource();
			self::assertInstanceOf(IResource::class, $resource);
			self::assertContains('/assets/foo.poo', (string)$resource->getUrl());
		}

		protected function tearDown() {
			if ($this->sqliteDriver) {
				$this->sqliteDriver->close();
			}
			$this->tempDirectory->delete();
		}
	}
