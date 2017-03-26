<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Config\ConfigurableTrait;

	class ResponseManager extends AbstractResponseHandler implements IResponseManager {
		use LazyConverterManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var IResponse
		 */
		protected $response;
		/**
		 * @var IResponseHandler
		 */
		protected $responseHandler;

		/**
		 * @inheritdoc
		 */
		public function registerResponseHandler(IResponseHandler $responseHandler = null): IResponseManager {
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
		public function response(IResponse $response = null): IResponseManager {
			$this->response = $response;
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
		public function send(IResponse $response): IResponseHandler {
			$this->converterManager->setup();
			$this->converterManager->content($response, $response->getTargetList())
				->convert();
			return $this;
		}
	}
