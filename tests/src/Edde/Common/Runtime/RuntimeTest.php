<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\File\RootDirectory;
	use Edde\Module\ContainerModule;
	use PHPUnit\Framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class RuntimeTest extends TestCase {
		public function testCommon() {
			$runtime = new Runtime();
			self::assertTrue($runtime->isConsoleMode());
		}

		public function testExecute() {
			$flag = false;
			(new Runtime())->registerFactoryList([
				IRootDirectory::class => new RootDirectory(__DIR__ . '/.'),
			])
				->module(new ContainerModule())
				->run(function (IRuntime $runtime, IContainer $container) use (&$flag) {
					$flag = true;
					self::assertTrue($runtime->isConsoleMode());
				});
			self::assertTrue($flag);
		}
	}
