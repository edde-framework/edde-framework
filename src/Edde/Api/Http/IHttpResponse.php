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
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return IHttpResponse
		 */
		public function send(): IHttpResponse;
	}
