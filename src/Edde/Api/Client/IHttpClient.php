<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Event\IEventBus;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Usable\IDeffered;

	interface IHttpClient extends IDeffered, IEventBus {
		/**
		 * do an arbitrary request; the all others are shortcut to this method
		 *
		 * @param IHttpRequest $httpRequest
		 *
		 * @return IHttpHandler
		 */
		public function request(IHttpRequest $httpRequest): IHttpHandler;

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
		public function delete($url): IHttpHandler;
	}
