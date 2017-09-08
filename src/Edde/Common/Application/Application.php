<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Router\LazyRequestTrait;
	use Edde\Common\Object\Object;

	class Application extends Object implements IApplication {
		use LazyProtocolServiceTrait;
		use LazyRequestTrait;
		use LazyLogServiceTrait;
		/**
		 * return code from an application
		 *
		 * @var int
		 */
		protected $code;

		/**
		 * @inheritdoc
		 */
		public function setCode(int $code): IApplication {
			$this->code = $code;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function run(): int {
			try {
				/**
				 * nothing special - protocol service is connected to service responsible for
				 * providing a request
				 */
				$this->protocolService->execute($this->request->getElement());
				return $this->code ?: 0;
			} catch (\Throwable $exception) {
				$this->logService->exception($exception, ['edde']);
				/**
				 * if somebody already set a code, respect it or try to guess one
				 *
				 * the code could be 0; so change it to something else to keep track of an
				 * error state of an application
				 */
				return $this->code ?: (($code = $exception->getCode()) === 0 ? -1 : $code);
			}
		}
	}
