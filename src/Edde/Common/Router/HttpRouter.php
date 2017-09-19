<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRequest;
	use Edde\Api\Runtime\LazyRuntimeTrait;

	class HttpRouter extends AbstractRouter {
		use LazyRuntimeTrait;
		use Edde\Api\Http\Inject\LazyHttpServiceTrait;

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
