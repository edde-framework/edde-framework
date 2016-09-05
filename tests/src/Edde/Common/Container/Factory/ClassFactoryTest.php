<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\DependencyException;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Callback\Parameter;
	use Edde\Common\ContainerTest\RecursiveClass;
	use Edde\Common\ContainerTest\TestCommonClass;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/../assets.php');

	class ClassFactoryTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			$factory = new ClassFactory('name', TestCommonClass::class, false, false);
			self::assertEquals('name', $factory->getName());
			self::assertEquals([
				'foo' => new Parameter('foo', null, false),
				'bar' => new Parameter('bar', null, false),
			], $factory->getParameterList());
			self::assertFalse($factory->isCloneable());
			self::assertFalse($factory->isSingleton());
			/** @var $alpha TestCommonClass */
			$alpha = $factory->create('name', [
				'a',
				'b',
			], $this->container);
			/** @var $beta TestCommonClass */
			$beta = $factory->create('name', [
				'b',
				'c',
			], $this->container);
			self::assertNotEquals($alpha, $beta);
			self::assertEquals('a', $alpha->getFoo());
			self::assertEquals('b', $alpha->getBar());
			self::assertEquals('b', $beta->getFoo());
			self::assertEquals('c', $beta->getBar());
		}

		public function testCloneable() {
			$factory = new ClassFactory('name', TestCommonClass::class, false, true);
			self::assertTrue($factory->isCloneable());
			self::assertFalse($factory->isSingleton());
			self::assertNotEquals($alpha = $factory->create('name', [
				'a',
				'b',
			], $this->container), $beta = $factory->create('name', [
				'a',
				'b',
			], $this->container));
			self::assertFalse($alpha->isCloned());
			self::assertTrue($beta->isCloned());
		}

		public function testSingleton() {
			$factory = new ClassFactory('name', TestCommonClass::class);
			self::assertFalse($factory->isCloneable());
			self::assertTrue($factory->isSingleton());
			self::assertEquals($factory->create('name', [
				'a',
				'b',
			], $this->container), $factory->create('name', [
				'a',
				'b',
			], $this->container));
		}

		public function testCircularDependency() {
			$this->expectException(DependencyException::class);
			$this->expectExceptionMessage('Detected recursive dependency [Edde\Common\ContainerTest\RecursiveClass] in stack [Edde\Common\ContainerTest\RecursiveClass].');
			$this->container->create(RecursiveClass::class);
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				RecursiveClass::class,
			]);
		}
	}
