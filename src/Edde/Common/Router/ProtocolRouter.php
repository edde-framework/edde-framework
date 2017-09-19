<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Http\Inject\HttpService;
	use Edde\Api\Protocol\Inject\ProtocolService;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Runtime\Inject\Runtime;
	use Edde\Common\Request\Message;

	/**
	 * Router to check if the protocol is able to handle incoming request.
	 */
	class ProtocolRouter extends AbstractRouter {
		use HttpService;
		use ProtocolService;
		use Runtime;

		public function canHandle(): bool {
			$request = $this->httpService->createRequest();
			$requestUrl = $request->getRequestUrl();
			$message = new Message($requestUrl->getPath(false));
			$message->appendAttributeList($requestUrl->getParameterList());
			return $this->runtime->isConsoleMode() === false && $this->protocolService->canHandle($message);
		}

		public function createRequest(): IRequest {
		}
	}
