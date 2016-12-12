<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Common\Cache\Cache;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\CallbackFactory;
	use PHPUnit\Framework\TestCase;

	class ContainerTest extends TestCase {
		public function testContainer() {
			$container = new Container();
			$container->registerFactory(new CallbackFactory(function () {
				return 'bar';
			}, 'foo'));
			$container->registerFactory(new CallbackFactory(function (): ICache {
				return new Cache(new InMemoryCacheStorage());
			}));
			self::assertEquals('bar', $container->create('foo'));
			self::assertEquals('bar', $container->call(function ($foo) {
				return $foo;
			}));
			self::assertInstanceOf(ICache::class, $container->create(ICache::class));
		}
	}
