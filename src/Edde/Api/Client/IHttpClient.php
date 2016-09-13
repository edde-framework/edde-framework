<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Usable\IUsable;

	interface IHttpClient extends IUsable {
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
		 * @param string|IUrl $url
		 * @param IPostList|mixed $post
		 * @param string|null $mime
		 *
		 * @return IHttpHandler
		 */
		public function post($url, $post, string $mime = null): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 * @param mixed $put used as a body
		 * @param string $mime used as a sent content type
		 *
		 * @return IHttpHandler
		 */
		public function put($url, $put, string $mime): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 * @param $delete
		 * @param string $mime
		 *
		 * @return IHttpHandler
		 */
		public function delete($url, $delete = null, string $mime = null): IHttpHandler;
	}
