<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Lock\LazyLockDirectoryTrait;
	use Edde\Api\Lock\LazyLockManagerTrait;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class LockManagerTest extends TestCase {
		use LazyLockManagerTrait;
		use LazyLockDirectoryTrait;

		public function testLocking() {
			$this->lockDirectory->purge();
			$lock = $this->lockManager->lock('foo');
			self::assertTrue($lock->isLocked());
			self::assertTrue($this->lockManager->isLocked('foo'));
			self::assertSame($lock, $this->lockManager->createLock('foo'));
		}

		public function testForignLock() {
			self::assertTrue($this->lockManager->isLocked('foo'));
		}

		public function testUnlockWithoutLock() {
			$this->expectException(ForeignLockException::class);
			$this->expectExceptionMessage('Lock [foo] cannot be unlocked because it was created by another lock (or in another thread). Use Edde\Api\Lock\ILock::kill() to kill the lock.');
			self::assertTrue($this->lockManager->isLocked('foo'));
			$this->lockManager->unlock('foo');
		}

		public function testKillLock() {
			$lock = $this->lockManager->kill('foo');
			self::assertFalse($lock->isLocked());
		}

		public function testLockLock() {
			$this->expectException(LockedException::class);
			$this->expectExceptionMessage('The name (id) [moo] is already locked.');
			$this->lockManager->lock('moo');
			$this->lockManager->lock('moo');
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__ . '/temp']),
				new ClassFactory(),
			]);
		}
	}
