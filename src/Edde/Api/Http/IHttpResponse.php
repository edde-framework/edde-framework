<?php
	namespace Edde\Api\Http;

	/**
	 * Simple interface for working with http response.
	 */
	interface IHttpResponse {
		/**
		 * return http response code
		 *
		 * @return int
		 */
		public function getCode();

		/**
		 * @return IHeaderList
		 */
		public function getHeaderList();

		/**
		 * @param IHeaderList $headerList
		 *
		 * @return $this
		 */
		public function setHeaderList(IHeaderList $headerList);

		/**
		 * @return ICookieList|ICookie[]
		 */
		public function getCookieList();

		/**
		 * @param ICookieList $cookieList
		 *
		 * @return $this
		 */
		public function setCookieList(ICookieList $cookieList);

		/**
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return void
		 */
		public function render();
	}
