<?php
	namespace Edde\Common\Container;

	use Edde\Common\Container\Factory\InstanceFactory;
	use phpunit\framework\TestCase;

	class FactoryManagerTest extends TestCase {
		public function testCommon() {
			$factoryManager = new FactoryManager();
			$factoryManager->registerFactory('foo', new InstanceFactory('foo', $this));
			self::assertTrue($factoryManager->hasFactory('foo'));
		}
	}
