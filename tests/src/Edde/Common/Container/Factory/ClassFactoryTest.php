<?php
	namespace Edde\Common\Container\Factory;

	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Callback\Parameter;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\ContainerTest\DummyCacheStorage;
	use Edde\Common\ContainerTest\TestCommonClass;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/../assets.php');

	class ClassFactoryTest extends TestCase {
		public function testCommon() {
			$factory = new ClassFactory('name', TestCommonClass::class, false, false);
			self::assertEquals('name', $factory->getName());
			self::assertEquals([
				'foo' => new Parameter('foo', null, false),
				'bar' => new Parameter('bar', null, false),
			], $factory->getParameterList());
			self::assertFalse($factory->isCloneable());
			self::assertFalse($factory->isSingleton());
			$container = $this->createDummyContainer();
			/** @var $alpha TestCommonClass */
			$alpha = $factory->create('name', [
				'a',
				'b',
			], $container);
			/** @var $beta TestCommonClass */
			$beta = $factory->create('name', [
				'b',
				'c',
			], $container);
			self::assertNotEquals($alpha, $beta);
			self::assertEquals('a', $alpha->getFoo());
			self::assertEquals('b', $alpha->getBar());
			self::assertEquals('b', $beta->getFoo());
			self::assertEquals('c', $beta->getBar());
		}

		public function createDummyContainer() {
			return new Container($factoryManager = new FactoryManager(), new DependencyFactory($factoryManager, $cacheFactory = new CacheFactory(__DIR__, new DummyCacheStorage())), $cacheFactory);
		}

		public function testCloneable() {
			$factory = new ClassFactory('name', TestCommonClass::class, false, true);
			self::assertTrue($factory->isCloneable());
			self::assertFalse($factory->isSingleton());
			$container = $this->createDummyContainer();
			self::assertNotEquals($alpha = $factory->create('name', [
				'a',
				'b',
			], $container), $beta = $factory->create('name', [
				'a',
				'b',
			], $container));
			self::assertFalse($alpha->isCloned());
			self::assertTrue($beta->isCloned());
		}

		public function testSingleton() {
			$factory = new ClassFactory('name', TestCommonClass::class);
			self::assertFalse($factory->isCloneable());
			self::assertTrue($factory->isSingleton());
			$container = $this->createDummyContainer();
			self::assertEquals($factory->create('name', [
				'a',
				'b',
			], $container), $factory->create('name', [
				'a',
				'b',
			], $container));
		}
	}
