<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	interface IResponse extends IHttp {
		/**
		 * set the http response code
		 *
		 * @param int $code
		 *
		 * @return IResponse
		 */
		public function setCode(int $code): IResponse;

		/**
		 * return http response code
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * set a location header
		 *
		 * @param string $redirect
		 *
		 * @return IResponse
		 */
		public function redirect(string $redirect): IResponse;
	}
