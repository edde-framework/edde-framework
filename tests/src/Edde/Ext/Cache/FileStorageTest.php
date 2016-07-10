<?php
	namespace Edde\Ext\Cache;

	use phpunit\framework\TestCase;

	class FileStorageTest extends TestCase {
		public function testCommon() {
			$storage = new FileCacheStorage(__DIR__ . '/cache', __NAMESPACE__);
			self::assertFalse($storage->isUsed());
			self::assertEquals(1, $storage->save('foo', 1));
			self::assertEquals(2, $storage->save('bar', 2));
			self::assertEquals(1, $storage->load('foo'));
			self::assertEquals(2, $storage->load('bar'));
			$storage->save('bar', null);
			self::assertNull($storage->load('bar'));
		}

		public function testCommon2() {
			$storage = new FileCacheStorage(__DIR__ . '/cache', __NAMESPACE__);
			self::assertFalse($storage->isUsed());
			self::assertEquals(1, $storage->load('foo'));
			$storage->invalidate();
			self::assertNull($storage->load('foo'));
		}
	}
