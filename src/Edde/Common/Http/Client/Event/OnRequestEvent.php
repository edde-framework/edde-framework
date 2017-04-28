<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http\Client\Event;

	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;

	/**
	 * Event emitted just before client call execution.
	 */
	class OnRequestEvent extends HandlerEvent {
		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler) {
			parent::__construct($httpRequest, $httpHandler);
			$this->cancel = false;
		}
	}
