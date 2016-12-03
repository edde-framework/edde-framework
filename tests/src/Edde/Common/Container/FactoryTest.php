<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Container\Factory\ClassFactory;
	use PHPUnit\Framework\TestCase;

	class FactoryTest extends TestCase {
		public function testClassFactory() {
			$factory = new ClassFactory();
			self::assertTrue($factory->canHandle($class = Container::class));
			self::assertFalse($factory->canHandle(IContainer::class));
			/**
			 * @var IParameter[] $mandatoryList
			 */
			self::assertNotEmpty($mandatoryList = $factory->getMandatoryList($class));
			$mandatoryList = array_values($mandatoryList);
//			self::assertEquals(IFactoryManager::class, $mandatoryList[0]->getClass());
//			self::assertEquals(ICacheManager::class, $mandatoryList[1]->getClass());
//			self::assertEmpty($factory->getInjectList(Container::class));
//			self::assertEmpty($factory->getLazyInjectList(Container::class));
		}
	}
