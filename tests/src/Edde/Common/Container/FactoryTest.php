<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Common\Cache\CacheManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ClassFactory;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class FactoryTest extends TestCase {
		public function testFactory() {
			$factoryManager = new FactoryManager(new CacheManager(new InMemoryCacheStorage()));
			$factoryManager->registerFactoryList([
				'foo' => function ($a, $b) {
				},
				function (ICache $cache): ICache {
					return $cache;
				},
				new ClassFactory(),
			]);
			$factory = $factoryManager->getFactory(\Something::class);
			self::assertSame($factory->dependency(\Something::class), $dependency = $factory->dependency(\Something::class));

			$factory = $factoryManager->getFactory('foo');
			self::assertSame($factory->dependency('foo'), $dependency = $factory->dependency('foo'));

			$factory = $factoryManager->getFactory(ICache::class);
			self::assertSame($factory->dependency(ICache::class), $dependency = $factory->dependency(ICache::class));
		}
	}
