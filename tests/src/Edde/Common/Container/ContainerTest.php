<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Cache\Cache;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\CallbackFactory;
	use Edde\Ext\Container\ClassFactory;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ContainerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testContainer() {
			self::assertEquals('bar', $this->container->create('foo'));
			self::assertEquals('bar', $this->container->call(function ($foo) {
				return $foo;
			}));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			/** @var $instance \Something */
			$instance = $this->container->create(\Something::class, 'fill-me-up');
			self::assertEquals('fill-me-up', $instance->someParameter);
			self::assertInstanceOf(\AnotherSomething::class, $instance->anotherSomething);
			self::assertInstanceOf(\InjectedSomething::class, $instance->injectedSomething);
			self::assertInstanceOf(\LazySomething::class, $instance->lazySomething);
			self::assertInstanceOf(\AnotherAnotherSomething::class, $instance->anotherAnotherSomething);
		}

		protected function setUp() {
			$this->container = new Container();
			$this->container->registerFactoryList([
				new CallbackFactory(function () {
					return 'bar';
				}, 'foo'),
				new CallbackFactory(function (): ICache {
					return new Cache(new InMemoryCacheStorage());
				}),
				new ClassFactory(),
			]);
		}
	}
