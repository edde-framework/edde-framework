<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Http\Inject\HttpService;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Runtime\Inject\Runtime;

	class HttpRouter extends AbstractRouter {
		use Runtime;
		use HttpService;

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
