<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Common\ContainerTest\BetaDependencyClass;
	use Edde\Common\ContainerTest\LazyInjectTraitClass;
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
