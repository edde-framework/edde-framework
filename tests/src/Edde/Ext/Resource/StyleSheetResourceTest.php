<?php
	namespace Edde\Ext\Resource;

	use Edde\Api\Resource\IResourceIndex;
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
	use Edde\Common\File\Directory;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Resource\FileStorage;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Resource\ResourceIndex;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Upgrade\UpgradeManager;
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
		 * @var IResourceIndex
		 */
		protected $resourceIndex;

		public function setUp() {
			FileUtils::recreate(__DIR__ . '/public');
			FileUtils::recreate(__DIR__ . '/temp');
			$cacheFactory = $cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage());
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactoryFallback(FactoryFactory::createFallback());
			$container = new Container($factoryManager, new DependencyFactory($factoryManager, $cacheFactory), $cacheFactory);
			$crateFactory = new CrateFactory($container);
			$this->storage = new DatabaseStorage($container, new SqliteDriver('sqlite:' . $this->getDatabaseFileName()), $cacheFactory);
			$this->schemaManager = new SchemaManager();
			$this->schemaManager->addSchema(new ResourceSchema());
			$this->upgradeManager = new UpgradeManager();
			$this->resourceIndex = new ResourceIndex($crateFactory, $this->schemaManager, $this->storage, new FilesystemScanner(__DIR__ . '/assets'), new Crypt());
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
			$styleSheetResource = new StyleSheetResource($fileStorage = new FileStorage($this->resourceIndex, new RootDirectory(__DIR__), new Directory(__DIR__ . '/public')), $this->resourceIndex, new TempDirectory(__DIR__ . '/temp'));
			$styleSheetResource->addStryleSheet(new Resource(FileUtils::url(__DIR__ . '/assets/css/font-awesome.css')));
			$styleSheetResource->addStryleSheet(new Resource(FileUtils::url(__DIR__ . '/assets/css/font-awesome.min.css')));
			$styleSheetResource->addStryleSheet(new Resource(FileUtils::url(__DIR__ . '/assets/css/simple-css.css')));
			self::assertFileExists($styleSheetResource->getUrl()
				->getAbsoluteUrl());
			$urlList = StringUtils::matchAll($styleSheetResource->get(), "~url\\((?<url>.*?)\\)~", true);
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
				self::assertTrue($this->resourceIndex->query()
					->urlLike('%' . $url)
					->hasResource(), sprintf('Missing resource [%s] in the resource index.', $url));
			}
			self::assertEquals(5, $count);
			$styleSheet = $fileStorage->getResource($styleSheetResource);
			self::assertFileExists($styleSheet->getUrl()
				->getAbsoluteUrl());
		}
	}
