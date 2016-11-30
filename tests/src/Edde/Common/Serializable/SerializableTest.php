<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serializable;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\FactoryManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use PHPUnit\Framework\TestCase;

	class SerializableTest extends TestCase {
		public function testSerializable() {
			$container = ContainerFactory::container([
				IContainer::class => Container::class,
				IFactoryManager::class => FactoryManager::class,
				ICacheManager::class => CacheManager::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
//				ICacheStorage::class => FlatFileCacheStorage::class,
//				ICacheDirectory::class => function () {
//					return new CacheDirectory(__DIR__ . '/temp');
//				},
			]);
			serialize($container);
		}
	}
