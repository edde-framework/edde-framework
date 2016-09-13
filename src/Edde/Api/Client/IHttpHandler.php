<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Http\IHttpResponse;

	/**
	 * When request is prepared bu a handler, client should create this handler for later execution.
	 */
	interface IHttpHandler {
		/**
		 * execute a client request
		 *
		 * @return IHttpResponse
		 */
		public function execute(): IHttpResponse;
	}
