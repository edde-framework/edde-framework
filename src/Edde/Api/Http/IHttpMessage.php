<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Http message implementation; message parsing can be heavy, so it is useful to make it deffered.
	 */
	interface IHttpMessage {
		/**
		 * return parsed input list of headers
		 *
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * return content type from input headers
		 *
		 * @param string $default
		 *
		 * @return null|string
		 */
		public function getContentType(string $default = ' application/octet-stream'): string;
	}
