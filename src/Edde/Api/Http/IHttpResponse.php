<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Simple interface for working with http response.
	 */
	interface IHttpResponse {
		/**
		 * set the http response code
		 *
		 * @param int $code
		 *
		 * @return IHttpResponse
		 */
		public function setCode(int $code): IHttpResponse;

		/**
		 * return http response code
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * @param IHeaderList $headerList
		 *
		 * @return IHttpResponse
		 */
		public function setHeaderList(IHeaderList $headerList): IHttpResponse;

		/**
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * shortcut to header list
		 *
		 * @param string $header
		 * @param string $value
		 *
		 * @return IHttpResponse
		 */
		public function header(string $header, string $value): IHttpResponse;

		/**
		 * @param ICookieList $cookieList
		 *
		 * @return IHttpResponse
		 */
		public function setCookieList(ICookieList $cookieList): IHttpResponse;

		/**
		 * @return ICookieList|ICookie[]
		 */
		public function getCookieList(): ICookieList;

		/**
		 * set a content type for a response
		 *
		 * @param string $contentType
		 *
		 * @return IHttpResponse
		 */
		public function contentType(string $contentType): IHttpResponse;

		/**
		 * set a location header
		 *
		 * @param string $redirect
		 *
		 * @return IHttpResponse
		 */
		public function redirect(string $redirect): IHttpResponse;

		/**
		 * return a response body
		 *
		 * @return IBody
		 */
		public function getBody(): IBody;

		/**
		 * retrieve decoded body or exception should be thrown
		 *
		 * @param string $target
		 * @param string|null $mime override incoming mime; it is not recommanded to use this option in common
		 *
		 * @return mixed
		 */
		public function body(string $target, $mime = null);

		/**
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return IHttpResponse
		 */
		public function send(): IHttpResponse;
	}
