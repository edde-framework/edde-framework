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

		/**
		 * retrieve decoded body or exception should be thrown
		 *
		 * @param string      $target
		 * @param string|null $mime override incoming mime; it is not recommanded to use this option in common
		 *
		 * @return mixed
		 */
		public function body(string $target, $mime = null);

		/**
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return IResponse
		 */
		public function send(): IResponse;
	}