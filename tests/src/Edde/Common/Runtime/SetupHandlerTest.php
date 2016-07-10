<?php
	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\RuntimeTest\DummyCacheStorage;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class SetupHandlerTest extends TestCase {
		public function testCommon() {
			$setupHandler = new SetupHandler($this->createCacheFactory());
			self::assertInstanceOf(IContainer::class, $setupHandler->createContainer());
		}

		protected function createCacheFactory() {
			return new CacheFactory(__DIR__, new DummyCacheStorage());
		}

		public function testMultirun() {
			$this->expectException(RuntimeException::class);
			$this->expectExceptionMessage('Cannot run [Edde\Common\Runtime\SetupHandler::createContainer()] multiple times; something is wrong!');
			$setupHandler = SetupHandler::create($this->createCacheFactory());
			$setupHandler->createContainer();
			$setupHandler->createContainer();
		}
	}
