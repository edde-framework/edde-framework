<?php
	declare(strict_types=1);

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

	/**
	 * @covers \Edde\Common\Container\Container<extended>
	 *
	 * @uses \Edde\Common\Cache\Cache<extended>
	 * @uses \Edde\Common\Container\AbstractFactory
	 * @uses \Edde\Common\Container\ConfigurableTrait
	 * @uses \Edde\Common\Container\Dependency
	 * @uses \Edde\Common\File\Directory
	 * @uses \Edde\Common\File\FileUtils
	 * @uses \Edde\Common\Reflection\ReflectionParameter
	 * @uses \Edde\Common\Reflection\ReflectionUtils
	 * @uses \Edde\Ext\Cache\FlatFileCacheStorage
	 * @uses \Edde\Ext\Cache\InMemoryCacheStorage
	 * @uses \Edde\Ext\Container\ClassFactory
	 * @uses \Edde\Ext\Container\ContainerFactory
	 * @uses \Edde\Ext\Container\InterfaceFactory
	 * @uses \Edde\Ext\Container\LinkFactory
	 * @uses \Edde\Ext\Container\ProxyFactory
	 * @uses \Edde\Ext\Container\SerializableFactory
	 */
	class ContainerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		protected $factoryList;
		protected $configList;

		public function testContainer() {
//			pclose(popen('start /B "" ' . escapeshellarg(PHP_BINARY) . ' -f runme.php', 'r'));
			self::assertSame($this->container, $this->container->create(IContainer::class));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
			/** @var $instance \Something */
			self::assertNotSame($instance = $this->container->create(\ISomething::class, ['fill-me-up']), $this->container->create(\Something::class, ['flush-me-out']));
			self::assertSame($this->container->create(\ISomething::class, ['fill-me-up']), $instance);
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
			$this->container = ContainerFactory::cache($this->factoryList, $this->configList, $cacheId = __DIR__ . '/cache/foo');
			file_put_contents($cacheId, serialize($this->container));
			$this->container = ContainerFactory::cache($this->factoryList, $this->configList, $cacheId);
			self::assertSame($this->container, $this->container->create(IContainer::class));
			self::assertInstanceOf(ICache::class, $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cache = $this->container->create(ICache::class));
			self::assertInstanceOf(ICacheManager::class, $cacheManager = $this->container->create(ICacheManager::class));
			self::assertSame($cache, $cacheManager);
			/** @var $instance \Something */
			self::assertNotSame($instance = $this->container->create(\ISomething::class, ['fill-me-up']), $this->container->create(\Something::class, ['flush-me-out']));
			self::assertSame($instance, $this->container->create(\ISomething::class, ['fill-me-up']));
			self::assertTrue($instance->isConfigured());
			self::assertNotEmpty($instance->somethingList);
			self::assertEquals([
				'foo',
				'bar',
				'boo',
			], $instance->somethingList);
			self::assertEquals('fill-me-up', $instance->someParameter);
		}

		/**
		 * @codeCoverageIgnore
		 */
		protected function setUp() {
			$cacheDirectory = new CacheDirectory(__DIR__ . '/cache');
			$cacheDirectory->purge();
			$this->container = ContainerFactory::container($this->factoryList = [
				\ISomething::class => \Something::class,
				ICacheDirectory::class => $cacheDirectory,
				ICacheStorage::class => FlatFileCacheStorage::class,
				\ThisIsProductOfCleverManager::class => \ThisIsCleverManager::class . '::createCleverProduct',
				new ClassFactory(),
			], $this->configList = [
				\ISomething::class => [
					\FirstSomethingSetup::class,
					\AnotherSomethingSetup::class,
				],
			]);
		}
	}
