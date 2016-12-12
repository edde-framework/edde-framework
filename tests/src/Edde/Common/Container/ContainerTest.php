<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Common\Cache\Cache;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\CallbackFactory;
	use Edde\Ext\Container\ClassFactory;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ContainerTest extends TestCase {
		public function testContainer() {
			$container = new Container();
			$container->registerFactoryList([
				new CallbackFactory(function () {
					return 'bar';
				}, 'foo'),
				new CallbackFactory(function (): ICache {
					return new Cache(new InMemoryCacheStorage());
				}),
				new ClassFactory(),
			]);
			self::assertEquals('bar', $container->create('foo'));
			self::assertEquals('bar', $container->call(function ($foo) {
				return $foo;
			}));
			self::assertInstanceOf(ICache::class, $container->create(ICache::class));
			/** @var $instance \Something */
			$instance = $container->create(\Something::class, 'fill-me-up');
			self::assertEquals('fill-me-up', $instance->someParameter);
			self::assertInstanceOf(\AnotherSomething::class, $instance->anotherSomething);
			self::assertInstanceOf(\InjectedSomething::class, $instance->injectedSomething);
			self::assertInstanceOf(\LazySomething::class, $instance->lazySomething);
			self::assertInstanceOf(\AnotherAnotherSomething::class, $instance->anotherAnotherSomething);
		}
	}
