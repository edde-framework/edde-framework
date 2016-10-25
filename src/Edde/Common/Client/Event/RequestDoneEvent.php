<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client\Event;

	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;

	class RequestDoneEvent extends HandlerEvent {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler, IHttpResponse $httpResponse) {
			parent::__construct($httpRequest, $httpHandler);
			$this->httpResponse = $httpResponse;
		}

		public function getHttpResponse(): IHttpResponse {
			return $this->httpResponse;
		}
	}
