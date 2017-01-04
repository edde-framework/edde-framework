<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Ext\Cache\FlatFileCacheStorage;
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
			self::assertSame($this->container, $this->container->create(IContainer::class));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
			/** @var $instance \Something */
			self::assertNotSame($instance = $this->container->create(\ISomething::class, 'fill-me-up'), $this->container->create(\Something::class, 'flush-me-out'));
			self::assertSame($this->container->create(\ISomething::class, 'fill-me-up'), $instance);
			$instance->config();
			self::assertNotEmpty($instance->somethingList);
			self::assertEquals([
				'foo',
				'bar',
				'boo',
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
			$this->container = unserialize($source = serialize($this->container));
			self::assertSame($this->container, $this->container->create(IContainer::class));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
			/** @var $instance \Something */
			self::assertNotSame($instance = $this->container->create(\ISomething::class, 'fill-me-up'), $this->container->create(\Something::class, 'flush-me-out'));
			self::assertSame($instance, $this->container->create(\ISomething::class, 'fill-me-up'));
			self::assertTrue($instance->isConfigured());
			self::assertNotEmpty($instance->somethingList);
			self::assertEquals([
				'foo',
				'bar',
				'boo',
			], $instance->somethingList);
			self::assertEquals('fill-me-up', $instance->someParameter);
		}

		protected function setUp() {
			$cacheDirectory = new CacheDirectory(__DIR__ . '/cache');
			$cacheDirectory->purge();
			$this->container = ContainerFactory::container([
				\ISomething::class => \Something::class,
				ICacheDirectory::class => $cacheDirectory,
				ICacheStorage::class => FlatFileCacheStorage::class,
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
