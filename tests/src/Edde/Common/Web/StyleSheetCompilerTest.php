<?php
	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Crypt\Crypt;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\Directory;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Resource\ResourceIndex;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Resource\Storage\FileStorage;
	use Edde\Common\Resource\Storage\StorageDirectory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;
	use phpunit\framework\TestCase;

	class StyleSheetCompilerTest extends TestCase {
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;
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

		public function setUp() {
			FileUtils::recreate(__DIR__ . '/public');
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
			$this->resourceIndex = new ResourceIndex($this->schemaManager, $this->storage, new FilesystemScanner(new Directory(__DIR__ . '/assets')), new Crypt());
			$factoryManager->registerFactory(ResourceStorable::class, FactoryFactory::create(ResourceStorable::class, [
				$this->resourceIndex,
				'createResourceStorable',
			], false));
			$this->upgradeManager->registerUpgrade(new InitialStorageUpgrade($this->storage, $this->schemaManager, '1.0'));
			$this->upgradeManager->upgrade();
			$this->resourceIndex->update();
		}

		protected function getDatabaseFileName() {
			return __DIR__ . '/temp/resource-test-' . sha1(microtime() . mt_rand(0, 99999)) . '.sqlite';
		}

		public function testCommon() {
			$styleSheetCompiler = new StyleSheetCompiler($fileStorage = new FileStorage($this->resourceIndex, new RootDirectory(__DIR__), new StorageDirectory(__DIR__ . '/public')), $this->tempDirectory);

			$resourceList = new ResourceList();
			$resourceList->addResource(new FileResource(__DIR__ . '/assets/css/font-awesome.css'));
			$resourceList->addResource(new FileResource(__DIR__ . '/assets/css/font-awesome.min.css'));
			$resourceList->addResource(new FileResource(__DIR__ . '/assets/css/simple-css.css'));

			$resource = $styleSheetCompiler->compile($resourceList);

			self::assertFileExists($resource->getUrl()
				->getAbsoluteUrl());
			$urlList = StringUtils::matchAll($resource->get(), "~url\\((?<url>.*?)\\)~", true);
			self::assertNotEmpty($urlList);
			self::assertArrayHasKey('url', $urlList);
			$count = 0;
			foreach (array_unique($urlList['url']) as $url) {
				$count++;
				$url = str_replace([
					'"',
					"'",
				], null, $url);
				self::assertFileExists(__DIR__ . '/' . $url);
			}
			self::assertEquals(5, $count);
		}

		protected function tearDown() {
			if ($this->sqliteDriver) {
				$this->sqliteDriver->close();
			}
			FileUtils::delete(__DIR__ . '/public');
			FileUtils::delete(__DIR__ . '/temp');
		}
	}
