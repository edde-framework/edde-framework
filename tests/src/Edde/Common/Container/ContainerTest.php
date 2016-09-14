<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IContainer;
	use Edde\Common\ContainerTest\AlphaDependencyClass;
	use Edde\Common\ContainerTest\BetaDependencyClass;
	use Edde\Common\ContainerTest\LazyInjectTraitClass;
	use Edde\Common\ContainerTest\LazyMissmatch;
	use Edde\Common\ContainerTest\SimpleClass;
	use Edde\Common\ContainerTest\SimpleDependency;
	use Edde\Common\ContainerTest\SimpleUnknownDependency;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class ContainerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			/**
			 * this is testing ability to include external parameter of a unknown (unregistered) class
			 */
			self::assertInstanceOf(SimpleClass::class, $this->container->create(SimpleClass::class, new SimpleUnknownDependency(), 1));
		}

		public function testLazyInject() {
			$lazyClass = $this->container->create(LazyInjectTraitClass::class);
			self::assertInstanceOf(BetaDependencyClass::class, $lazyClass->foo());
			self::assertInstanceOf(AlphaDependencyClass::class, $lazyClass->bar());
		}

		public function testLazyMissmatch() {
			$this->expectException(ContainerException::class);
			$this->expectExceptionMessage('Lazy inject missmatch: parameter [$betaDependencyClass] of method [Edde\Common\ContainerTest\LazyMissmatch::lazyDependency()] must have a property [Edde\Common\ContainerTest\LazyMissmatch::$betaDependencyClass] with the same name as the paramete (for example protected $betaDependencyClass).');
			$this->container->create(LazyMissmatch::class);
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				SimpleClass::class,
				SimpleDependency::class,
				LazyInjectTraitClass::class,
				BetaDependencyClass::class,
			]);
		}
	}
