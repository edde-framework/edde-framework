<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Container\IContainer;
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
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
			/** @var $instance \Something */
			self::assertNotSame($instance = $this->container->create(\ISomething::class, 'fill-me-up'), $this->container->create(\Something::class, 'flush-me-out'));
			$instance->config();
			self::assertNotEmpty($instance->somethingList);
			self::assertEquals([
				'foo',
				'bar',
			], $instance->somethingList);
			self::assertEquals('fill-me-up', $instance->someParameter);
			self::assertInstanceOf(\AnotherSomething::class, $instance->anotherSomething);
			self::assertInstanceOf(\InjectedSomething::class, $instance->injectedSomething);
			self::assertInstanceOf(\LazySomething::class, $instance->lazySomething);
			self::assertInstanceOf(\AnotherAnotherSomething::class, $instance->anotherAnotherSomething);
			self::assertInstanceOf(\ThisIsProductOfCleverManager::class, $this->container->create(\ThisIsProductOfCleverManager::class));
		}

		public function testContainerSerialization() {
			/** @noinspection UnserializeExploitsInspection */
			$this->container = unserialize(serialize($this->container));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
		}

		protected function setUp() {
			$this->container = ContainerFactory::container([
				\ISomething::class => \Something::class,
				\ThisIsProductOfCleverManager::class => \ThisIsCleverManager::class . '::createCleverProduct',
				new ClassFactory(),
			], [
				\ISomething::class => [
					\FirstSomethingSetup::class,
					\AnotherSomethingSetup::class,
				],
			]);
		}
	}
