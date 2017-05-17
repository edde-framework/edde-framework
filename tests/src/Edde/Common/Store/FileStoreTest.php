<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Lock\LazyLockDirectoryTrait;
	use Edde\Api\Store\LazyStoreDirectoryTrait;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Lock\ForeignLockException;
	use Edde\Common\Lock\LockedException;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class FileStoreTest extends TestCase {
		use LazyLockDirectoryTrait;
		use LazyStoreDirectoryTrait;
		use LazyStoreTrait;

		public function testLock() {
			$this->lockDirectory->purge();
			$this->storeDirectory->purge();
			$this->expectException(LockedException::class);
			$this->expectExceptionMessage('The name (id) [Edde\Common\Store\FileStore/foo] is already locked.');
			$this->assertInstanceOf(FileStore::class, $this->store);
			$this->store->lock('foo');
			$this->store->lock('foo', false);
		}

		public function testRepetativeLock() {
			$this->assertFalse($this->store->isLocked());
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
			$this->store->unlock();
			$this->assertFalse($this->store->isLocked());
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
			$this->store->unlock();
			$this->assertFalse($this->store->isLocked());
		}

		public function testThreadLock() {
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
		}

		public function testUnlockKaboom() {
			$this->expectException(ForeignLockException::class);
			$this->expectExceptionMessage('Lock [Edde\Common\Store\FileStore] cannot be unlocked because it was created by another lock (or in another thread). Use Edde\Api\Lock\ILock::kill() to kill the lock.');
			$this->store->unlock();
		}

		public function testThreadUnlock() {
			$this->assertTrue($this->store->isLocked());
			$this->store->kill();
			$this->assertFalse($this->store->isLocked());
		}

		public function testSaveData() {
			$this->store->set('foo', 'yapee!');
			self::assertEquals('yapee!', $this->store->get('foo'));
			self::assertEquals('this is default', $this->store->get('moo', 'this is default'));
		}

		public function testThreadedData() {
			self::assertEquals('yapee!', $this->store->get('foo'));
		}

		public function testLockingUnlocked() {
			self::assertFalse($this->store->isLocked('lock-this'));
			$this->store->setExclusive('lock-this', 'value');
			self::assertFalse($this->store->isLocked('lock-this'));
			self::assertEquals('value', $this->store->get('lock-this'));
		}

		public function testLockingLocked() {
			$this->expectException(LockedException::class);
			$this->expectExceptionMessage('The name (id) [Edde\Common\Store\FileStore/lock-this] is already locked.');
			$this->store->lock('lock-this');
			$this->store->setExclusive('lock-this', 'value');
		}

		public function testExclusive() {
			$this->expectException(LockedException::class);
			$this->expectExceptionMessage('The name (id) [Edde\Common\Store\FileStore/lock-this] is already locked.');
			$this->store->setExclusive('lock-this', 'value');
		}

		public function testExclusiveUnlock() {
			$this->store->kill('lock-this');
			$this->store->setExclusive('lock-this', 'another-value');
			self::assertEquals('another-value', $this->store->get('lock-this'));
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
