<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Router\IRequest;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Router\AbstractRouter;

	class HttpRouter extends AbstractRouter {
		use LazyRuntimeTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(): bool {
			return $this->runtime->isConsoleMode() === false;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
		}
	}
