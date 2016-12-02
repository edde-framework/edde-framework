<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Common\Container\Factory\ClassFactory;
	use PHPUnit\Framework\TestCase;

	class FactoryTest extends TestCase {
		public function testClassFactory() {
			$factory = new ClassFactory();
			self::assertTrue($factory->canHandle(Container::class));
			self::assertFalse($factory->canHandle(IContainer::class));
		}
	}
