<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serializable;

	use PHPUnit\Framework\TestCase;

	class SerializableTest extends TestCase {
		public function testSerializable() {
//			$container = ContainerFactory::container([
//				IContainer::class => Container::class,
//				IFactoryManager::class => FactoryManager::class,
//				ICacheManager::class => CacheManager::class,
//				ICacheStorage::class => InMemoryCacheStorage::class,
//				ICacheStorage::class => FlatFileCacheStorage::class,
//				ICacheDirectory::class => function () {
//					return new CacheDirectory(__DIR__ . '/temp');
//				},
//			]);
			self::assertTrue(true);
		}
	}
