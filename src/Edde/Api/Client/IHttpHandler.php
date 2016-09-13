<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpResponse;

	/**
	 * When request is prepared bu a handler, client should create this handler for later execution.
	 */
	interface IHttpHandler {
		/**
		 * @param string $authorization
		 *
		 * @return IHttpHandler
		 */
		public function authorization(string $authorization): IHttpHandler;

		/**
		 * @return IHttpHandler
		 */
		public function keepConnectionAlive(): IHttpHandler;

		/**
		 * this should modify an original http request class (if used)
		 *
		 * @param string $name
		 * @param string $value
		 *
		 * @return IHttpHandler
		 */
		public function header(string $name, string $value): IHttpHandler;

		/**
		 * method build round body method (internally should create IBody class)
		 *
		 * @param mixed $content
		 * @param string $mime
		 * @param string $target
		 *
		 * @return IHttpHandler
		 */
		public function content($content, string $mime, string $target): IHttpHandler;

		/**
		 * @param IBody $body
		 *
		 * @return IHttpHandler
		 */
		public function body(IBody $body): IHttpHandler;

		/**
		 * execute a client request
		 *
		 * @return IHttpResponse
		 */
		public function execute(): IHttpResponse;
	}
