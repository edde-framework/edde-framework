<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Common\Object\Object;

	class Application extends Object implements IApplication {
		use LazyProtocolServiceTrait;
		use LazyRouterServiceTrait;
		use LazyLogServiceTrait;

		public function run(): int {
			try {
				$this->protocolService->execute($this->routerService->createElement());
				return 0;
			} catch (\Throwable $exception) {
				$this->logService->exception($exception, ['edde']);
				return $exception->getCode();
			}
		}
	}
