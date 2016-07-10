<?php
	namespace Edde\Api\Http;

	use Edde\Api\Url\IUrl;

	/**
	 * Interface describing http request; it can has arbitrary usage, not only for wrapping of
	 * PHP's $_REQUEST/... variables.
	 */
	interface IHttpRequest {
		/**
		 * @return IUrl
		 */
		public function getUrl();

		/**
		 * @return string
		 */
		public function getMethod();

		/**
		 * @param string $method
		 *
		 * @return bool
		 */
		public function isMethod($method);

		/**
		 * @return IPostList
		 */
		public function getPostList();

		/**
		 * @param IPostList $postList
		 *
		 * @return $this
		 */
		public function setPostList(IPostList $postList);

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
		 * @return ICookieList
		 */
		public function getCookieList();

		/**
		 * @param ICookieList $cookieList
		 *
		 * @return $this
		 */
		public function setCookieList(ICookieList $cookieList);

		/**
		 * @return null|string
		 */
		public function getRemoteAddress();

		/**
		 * @return null|string
		 */
		public function getRemoteHost();

		/**
		 * @return IUrl|null
		 */
		public function getReferer();

		/**
		 * @return bool
		 */
		public function isSecured();

		/**
		 * @return bool
		 */
		public function isAjax();

		/**
		 * @return mixed
		 */
		public function getBody();

		/**
		 * bind response to this request; in common case this response should be sent to a client
		 *
		 * @param IHttpResponse $httpResponse
		 *
		 * @return $this
		 */
		public function setResponse(IHttpResponse $httpResponse);

		/**
		 * response should be always set (for example status 200, 404, 500, ...)
		 *
		 * @return IHttpResponse
		 */
		public function getResponse();
	}
