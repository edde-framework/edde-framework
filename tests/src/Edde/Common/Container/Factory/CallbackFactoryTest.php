<?php
	namespace Edde\Common\Container\Factory;

	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\ContainerTest\DummyCacheStorage;
	use Edde\Common\ContainerTest\MagicFactory;
	use Edde\Common\ContainerTest\TestMagicFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/../assets.php');

	class CallbackFactoryTest extends TestCase {
		public function testCommon() {
			$factory = new CallbackFactory('name', $magicFactory = new MagicFactory(), false, false);
			self::assertFalse($factory->isCloneable());
			self::assertFalse($factory->isSingleton());
			self::assertFalse($magicFactory->hasFlag());
			self::assertEquals('name', $factory->getName());
			self::assertInstanceOf(TestMagicFactory::class, $factory->create('name', [], $this->createDummyContainer()));
			self::assertTrue($magicFactory->hasFlag());
		}

		public function createDummyContainer() {
			return new Container($factoryManager = new FactoryManager(), new DependencyFactory($factoryManager, $cacheFactory = new CacheFactory(__DIR__, new DummyCacheStorage())), $cacheFactory);
		}
	}
