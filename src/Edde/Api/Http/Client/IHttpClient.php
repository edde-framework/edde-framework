<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client;

	use Edde\Api\Http\IRequest;
	use Edde\Api\Url\IUrl;

	interface IHttpClient {
		/**
		 * do an arbitrary request; the all others are shortcut to this method
		 *
		 * @param IRequest $request
		 *
		 * @return IHttpHandler
		 */
		public function request(IRequest $request): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function get($url): IHttpHandler;

		/**
		 * @param string|IUrl $url target url address
		 *
		 * @return IHttpHandler
		 */
		public function post($url): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function put($url): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function patch($url): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function delete($url): IHttpHandler;
	}
