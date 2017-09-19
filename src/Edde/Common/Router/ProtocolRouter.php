<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Http\Inject\HttpService;
	use Edde\Api\Protocol\IElement;
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
		/**
		 * @var IElement
		 */
		protected $element;

		public function canHandle(): bool {
			if ($this->runtime->isConsoleMode()) {
				return $this->canHandleCli();
			}
			return $this->canHandleHttp();
		}

		protected function canHandleHttp(): bool {
			$request = $this->httpService->createRequest();
			$requestUrl = $request->getRequestUrl();
			$this->element = $message = new Message($requestUrl->getPath(false));
			$message->appendAttributeList($requestUrl->getParameterList());
			return $this->protocolService->canHandle($message);
		}

		protected function canHandleCli(): bool {
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			return new Request($this->element);
		}
	}
