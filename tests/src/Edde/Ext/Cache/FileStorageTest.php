<?php
	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\File\FileUtils;
	use phpunit\framework\TestCase;

	class FileStorageTest extends TestCase {
		/**
		 * @var ICacheDirectory
		 */
		protected $cacheDirectory;

		public static function tearDownAfterClass() {
			FileUtils::delete(__DIR__ . '/cache');
		}

		public function testCommon() {
			$storage = new FileCacheStorage($this->cacheDirectory, __NAMESPACE__);
			self::assertFalse($storage->isUsed());
			self::assertEquals(1, $storage->save('foo', 1));
			self::assertEquals(2, $storage->save('bar', 2));
			self::assertEquals(1, $storage->load('foo'));
			self::assertEquals(2, $storage->load('bar'));
			$storage->save('bar', null);
			self::assertNull($storage->load('bar'));
		}

		public function testCommon2() {
			$storage = new FileCacheStorage($this->cacheDirectory, __NAMESPACE__);
			self::assertFalse($storage->isUsed());
			self::assertEquals(1, $storage->load('foo'));
			$storage->invalidate();
			self::assertNull($storage->load('foo'));
		}

		protected function setUp() {
			$this->cacheDirectory = new CacheDirectory(__DIR__ . '/cache');
		}
	}
