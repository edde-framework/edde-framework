<?php
	declare(strict_types=1);
	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Container\Inject\Container;
	use Edde\Ext\Test\TestCase;
	use function microtime;

	class ApplicationTest extends TestCase {
		use Container;

		public function testApplication() {
			$limit = 1000;
			$start = microtime(true);
			for ($i = 0; $i < $limit; $i++) {
				$application = ApplicationService::get();
				$application->run();
			}
			printf("static %.4f\n", microtime(true) - $start);
			$start = microtime(true);
			for ($i = 0; $i < $limit; $i++) {
				$application = $this->container->create(IApplication::class);
				$application->run();
			}
			printf("container %.4f\n", microtime(true) - $start);
		}
	}
