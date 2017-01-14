<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * "Abstract" interface holding common stuff between request and repsonse.
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
		 * @return $this
		 */
		public function header(string $header, string $value);

		/**
		 * @return IBody|null
		 */
		public function getBody();
	}
