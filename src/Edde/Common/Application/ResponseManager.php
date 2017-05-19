<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Config\ConfigurableTrait;

	class ResponseManager extends AbstractResponseHandler implements IResponseManager {
		use LazyConverterManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var IElement
		 */
		protected $response;
		/**
		 * @var IResponseHandler
		 */
		protected $responseHandler;

		/**
		 * @inheritdoc
		 */
		public function setResponseHandler(IResponseHandler $responseHandler = null): IResponseManager {
			$this->responseHandler = $responseHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasResponse(): bool {
			return $this->response !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function response(IElement $element): IResponseManager {
			$this->response = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute() {
			if ($this->response === null) {
				return;
			}
			$this->responseHandler = $this->responseHandler ?: $this;
			$this->responseHandler->setup();
			$this->responseHandler->send($this->response);
		}

		/**
		 * @inheritdoc
		 */
		public function send(IElement $element): IResponseHandler {
			throw new \Exception('not implemented yet: ' . __METHOD__);
			$this->converterManager->setup();
			$this->converterManager->content($element, $element->getTargetList())->convert();
			return $this;
		}
	}
