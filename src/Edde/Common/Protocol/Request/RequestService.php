<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IError;
	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Api\Protocol\Request\IRequest;
	use Edde\Api\Protocol\Request\IRequestHandler;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Protocol\Request\IResponse;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Protocol\Error;

	class RequestService extends AbstractRequestHandler implements IRequestService {
		/**
		 * @var IRequestHandler[]
		 */
		protected $requestHandlerList = [];
		/**
		 * @var IResponse[]
		 */
		protected $responseList = [];

		/**
		 * @inheritdoc
		 */
		public function registerRequestHandler(IRequestHandler $requestHandler): IRequestService {
			$this->requestHandlerList[] = $requestHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getResponseList(): array {
			return $this->responseList;
		}

		/**
		 * @inheritdoc
		 */
		public function request(IRequest $request): IElement {
			if (isset($this->responseList[$id = $request->getId()])) {
				return $this->responseList[$id];
			}
			return $this->execute($request);
		}

		/**
		 * @inheritdoc
		 *
		 * @param IMessage|IRequest|IError $element
		 */
		protected function element(IElement $element) {
			foreach ($this->requestHandlerList as $requestHandler) {
				if ($requestHandler->canHandle($element) && ($response = $requestHandler->execute($element)) instanceof IResponse) {
					return $this->responseList[$element->getId()] = $response;
				}
			}
			$error = new Error($element, 100, sprintf('Unhandled request [%s (%s)].', $element->getRequest(), get_class($element)));
			$error->setReference($element);
			$error->setException(UnhandledRequestException::class);
			return $error;
		}
	}
