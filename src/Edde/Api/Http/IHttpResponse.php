<?php
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
		 * @return $this
		 */
		public function setCode($code);

		/**
		 * return http response code
		 *
		 * @return int
		 */
		public function getCode();

		/**
		 * @param IHeaderList $headerList
		 *
		 * @return $this
		 */
		public function setHeaderList(IHeaderList $headerList);

		/**
		 * @return IHeaderList
		 */
		public function getHeaderList();

		/**
		 * @param ICookieList $cookieList
		 *
		 * @return $this
		 */
		public function setCookieList(ICookieList $cookieList);

		/**
		 * @return ICookieList|ICookie[]
		 */
		public function getCookieList();

		/**
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function setRenderCallback(callable $callback);

		/**
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return void
		 */
		public function render();
	}
