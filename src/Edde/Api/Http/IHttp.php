<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	/**
	 * "Abstract" interface holding common stuff between request and response.
	 */
	interface IHttp {
		/**
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * @return ICookieList
		 */
		public function getCookieList(): ICookieList;

		/**
		 * shortcut to header list
		 *
		 * @param string $header
		 * @param string $value
		 *
		 * @return IHttp
		 */
		public function header(string $header, string $value): IHttp;

		/**
		 * set a content type
		 *
		 * @param string $contentType
		 *
		 * @return IHttp
		 */
		public function setContentType(string $contentType): IHttp;

		/**
		 * @return IBody|null
		 */
		public function getBody();
	}
