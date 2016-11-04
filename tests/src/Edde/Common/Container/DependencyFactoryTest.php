<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Common\Cache\DummyCacheManager;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\ContainerTest\AlphaDependencyClass;
	use Edde\Common\ContainerTest\BetaDependencyClass;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets.php';

	class DependencyFactoryTest extends TestCase {
		public function testCommon() {
			$factoryManager = new FactoryManager($cacheFactory = new DummyCacheManager());
			$dependencyFactory = new DependencyFactory($factoryManager, $cacheFactory);
			$factoryManager->registerFactory('foo', new CallbackFactory('foo', function (AlphaDependencyClass $foo, BetaDependencyClass $bar) {
			}));
			$factoryManager->registerFactoryList([
				AlphaDependencyClass::class => AlphaDependencyClass::class,
				BetaDependencyClass::class => BetaDependencyClass::class,
			]);
			self::assertInstanceOf(IDependency::class, $dependency = $dependencyFactory->create('foo'));
			self::assertEquals('foo', $dependency->getName());
			$parent = new Dependency('foo', false, false, 'foo');
			$parent->addNode(new Dependency('foo', true, false, AlphaDependencyClass::class));
			$parent->addNode(new Dependency('bar', true, false, BetaDependencyClass::class));
			self::assertEquals($parent->getNodeList(), $dependency->getNodeList());
		}
	}
