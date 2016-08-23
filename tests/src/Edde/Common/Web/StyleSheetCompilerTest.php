<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Resource\Storage\FileStorage;
	use Edde\Common\Resource\Storage\StorageDirectory;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Cache\FileCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
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
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;

		public function setUp() {
			FileUtils::recreate(__DIR__ . '/public');
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();
			$cacheFactory = new CacheFactory(__DIR__, new DevNullCacheStorage());
			$this->storage = new DatabaseStorage($this->sqliteDriver = new SqliteDriver('sqlite:' . $this->getDatabaseFileName()), $cacheFactory);
			$this->schemaManager = new SchemaManager(new SchemaFactory(new ResourceManager()));
			$this->upgradeManager = new UpgradeManager();
			$container = ContainerFactory::create([
				IStorage::class => $this->storage,
				ISchemaManager::class => $this->schemaManager,
			]);
			$this->upgradeManager->registerUpgrade($upgrade = new InitialStorageUpgrade());
			$container->inject($upgrade);
			$this->upgradeManager->upgrade();
		}

		protected function getDatabaseFileName() {
			return $this->tempDirectory->filename('resource-test-' . sha1(microtime() . random_int(0, 99999)) . '.sqlite');
		}

		public function testCommon() {
			$styleSheetCompiler = new StyleSheetCompiler();
			$styleSheetCompiler->lazyFileStorage($fileStorage = new FileStorage(new RootDirectory(__DIR__), new StorageDirectory(__DIR__ . '/public')));
			$styleSheetCompiler->lazyTempDirectory($this->tempDirectory);
			$styleSheetCompiler->injectCacheFactory(new CacheFactory(__DIR__, new FileCacheStorage(new CacheDirectory(__DIR__ . '/temp'))));

			$resourceList = new ResourceList();
			$resourceList->addResource(new File(__DIR__ . '/assets/css/font-awesome.css'));
			$resourceList->addResource(new File(__DIR__ . '/assets/css/font-awesome.min.css'));
			$resourceList->addResource(new File(__DIR__ . '/assets/css/simple-css.css'));

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
