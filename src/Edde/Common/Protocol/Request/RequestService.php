<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Api\Protocol\Request\IRequest;
	use Edde\Api\Protocol\Request\IRequestHandler;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Protocol\Request\IResponse;
	use Edde\Api\Protocol\Request\UnhandledRequestException;

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
		public function request(IRequest $request): IResponse {
			if (isset($this->responseList[$id = $request->getId()])) {
				return $this->responseList[$id];
			}
			return $this->execute($request);
		}

		/**
		 * @inheritdoc
		 *
		 * @param IMessage|IRequest $element
		 */
		protected function element(IElement $element) {
			foreach ($this->requestHandlerList as $requestHandler) {
				if ($requestHandler->canHandle($element)) {
					if (($response = $requestHandler->execute($element)) instanceof IResponse) {
						$this->responseList[$element->getId()] = $response;
					}
					return $response;
				}
			}
			throw new UnhandledRequestException(sprintf('Unhandled request [%s (%s)].', $element->getRequest(), get_class($element)));
		}
	}
