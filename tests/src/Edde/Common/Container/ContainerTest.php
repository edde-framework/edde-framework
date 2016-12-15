<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Cache\CacheManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
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

			/**
			 * Container::getFactory - cache results in memory and cache results in cache and see benchmark
			 */
//
//			Debugger::timer('foo');
//			for ($i = 0; $i <= 100000; $i++) {
//				$this->container->create(\Something::class, 'fill-me-up');
//			}
//			printf("%.2f\n", Debugger::timer('foo') );
		}

		protected function setUp() {
			$this->container = ContainerFactory::crate([
				IContainer::class => Container::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
				ICacheManager::class => CacheManager::class,
				ICache::class => ICacheManager::class,
				\ISomething::class => \Something::class,
				new ClassFactory(),
			]);
		}
	}
