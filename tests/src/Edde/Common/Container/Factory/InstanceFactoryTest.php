<?php
	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\ContainerTest\DummyCacheStorage;
	use Edde\Framework;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/../assets.php');

	class InstanceFactoryTest extends TestCase {
		public function testCommon() {
			$factory = new InstanceFactory('name', $this);
			self::assertEquals('name', $factory->getName());
			self::assertEmpty($factory->getParameterList());
			self::assertSame($this, $factory->create('name', [], $container = $this->createDummyContainer()));
			self::assertSame($this, $factory->create('name', [], $container));
			self::assertFalse($factory->isCloneable());
			self::assertTrue($factory->isSingleton());
		}

		public function createDummyContainer() {
			return new Container($factoryManager = new FactoryManager(), new DependencyFactory($factoryManager, $cacheFactory = new CacheFactory(__DIR__, new DummyCacheStorage())), $cacheFactory);
		}

		public function testFactoryException() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Something went wrong. God will kill one cute kitten and The Deep Evil of The Most Evilest Hell will eat it!');
			$factory = new InstanceFactory('name', $this);
			$factory->factory([], $this->createDummyContainer());
		}

		public function testOnSetup() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Cannot register onSetup handler on [Edde\Common\Container\Factory\InstanceFactory]; setup handlers are not supported by this factory.');
			$factory = new InstanceFactory('name', $this);
			$factory->onSetup(function () {
			});
		}
	}
