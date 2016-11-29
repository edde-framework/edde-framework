<?php
	namespace Edde\Ext\Cache;

	use PHPUnit\Framework\TestCase;

	class InMemoryStorageTest extends TestCase {
		public function testCommon() {
			$storage = new InMemoryCacheStorage();
			self::assertTrue($storage->save('foo', true));
			self::assertTrue($storage->load('foo'));
			$storage->invalidate();
			self::assertNull($storage->load('foo'));
		}
	}
