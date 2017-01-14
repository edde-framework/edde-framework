<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client\Event;

	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;

	/**
	 * Basic event when handler is created (before execution)
	 */
	class HandlerEvent extends ClientEvent {
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var \Edde\Api\Http\Client\IHttpHandler
		 */
		protected $httpHandler;

		/**
		 * @param IHttpRequest                       $httpRequest
		 * @param \Edde\Api\Http\Client\IHttpHandler $httpHandler
		 */
		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler) {
			$this->httpRequest = $httpRequest;
			$this->httpHandler = $httpHandler;
		}

		/**
		 * @return IHttpRequest
		 */
		public function getHttpRequest(): IHttpRequest {
			return $this->httpRequest;
		}

		/**
		 * @return \Edde\Api\Http\Client\IHttpHandler
		 */
		public function getHttpHandler(): IHttpHandler {
			return $this->httpHandler;
		}
	}
