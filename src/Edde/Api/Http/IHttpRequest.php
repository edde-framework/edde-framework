<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	use Edde\Api\Url\IUrl;

	/**
	 * Interface describing http request; it can has arbitrary usage, not only for wrapping of
	 * PHP's $_REQUEST/... variables.
	 */
	interface IHttpRequest {
		/**
		 * @return IRequestUrl
		 */
		public function getRequestUrl(): IRequestUrl;

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
		 * @param IBody|null $body
		 *
		 * @return IHttpRequest
		 */
		public function setBody(IBody $body = null): IHttpRequest;

		/**
		 * @return IBody|null
		 */
		public function getBody();
	}
