<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Wrapper factory for creating http requests and responses.
	 */
	interface IHttpRequestFactory {
		/**
		 * return http request; factory should create a new request per call
		 *
		 * @return IHttpRequest
		 */
		public function create();
	}
