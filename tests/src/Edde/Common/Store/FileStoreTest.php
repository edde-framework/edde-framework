<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class FileStoreTest extends TestCase {
		use LazyStoreTrait;

		public function testLock() {
			self::expectException(StoreLockedException::class);
			self::assertInstanceOf(FileStore::class, $this->store);
			$this->store->lock('foo');
			$this->store->lock('foo', false);
		}

		public function testRepetativeLock() {
			$this->store->lock();
			$this->store->unlock();
			$this->store->lock();
			$this->store->unlock();
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__ . '/temp']),
				new ClassFactory(),
			]);
		}
	}
