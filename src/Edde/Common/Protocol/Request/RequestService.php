<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\IRequestHandler;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Protocol\Element;

	class RequestService extends AbstractRequestHandler implements IRequestService {
		use LazyContainerTrait;
		/**
		 * @var IRequestHandler[]
		 */
		protected $requestHandlerList = [];
		/**
		 * @var IElement[]
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
		public function request(IElement $element): INode {
			if (isset($this->responseList[$id = $element->getAttribute('id')])) {
				return $this->responseList[$id];
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			foreach ($this->requestHandlerList as $requestHandler) {
				/** @var $response IElement */
				if ($requestHandler->canHandle($element) && ($response = $requestHandler->execute($element)) instanceof IElement) {
					return $this->responseList[$element->getId()] = $response->setReference($element);
				}
			}
			return new Element('error', null, [
				'reference' => $element,
				'code'      => 100,
				'message'   => sprintf('Unhandled request [%s].', $element->getAttribute('request')),
				'exception' => UnhandledRequestException::class,
			]);
		}
	}
