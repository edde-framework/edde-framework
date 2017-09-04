<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Router\LazyRequestTrait;
	use Edde\Common\Object\Object;

	class Application extends Object implements IApplication {
		use LazyProtocolServiceTrait;
		use LazyRequestTrait;
		use LazyLogServiceTrait;

		public function run(): int {
			try {
				/** @var $response IElement */
				if (($response = $this->protocolService->execute($this->request->getElement())) instanceof IElement) {
					return (int)$response->getMeta('code', 0);
				}
				return 0;
			} catch (\Throwable $exception) {
				$this->logService->exception($exception, ['edde']);
				/**
				 * the code could be 0; so change it to something else to keep track of an
				 * error state of an application
				 */
				return ($code = $exception->getCode()) === 0 ? -1 : $code;
			}
		}
	}
