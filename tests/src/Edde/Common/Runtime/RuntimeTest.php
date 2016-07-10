<?php
	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\RuntimeTest\DummyCacheStorage;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class RuntimeTest extends TestCase {
		public function testCommon() {
			$runtime = new Runtime($setupHandler = new SetupHandler($this->createCacheFactory()));
			self::assertFalse($runtime->isUsed());
			self::assertTrue($runtime->isConsoleMode());
		}

		protected function createCacheFactory() {
			return new CacheFactory(__DIR__, new DummyCacheStorage());
		}

		public function testExecute() {
			$setupHandler = new SetupHandler($this->createCacheFactory());
			$flag = false;
			Runtime::execute($setupHandler, function (IRuntime $runtime, IContainer $container) use (&$flag) {
				$flag = true;
				self::assertTrue($runtime->isConsoleMode());
			});
			self::assertTrue($flag);
		}
	}
