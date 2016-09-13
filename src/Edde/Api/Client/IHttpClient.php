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
		 * @param string|IUrl $url target url address
		 * @param IPostList|mixed $post if post is arbitrary data, type and mime must be supplied
		 * @param string|null $mime target mime type (also target for conversion)
		 *
		 * @param string $target
		 *
		 * @return IHttpHandler
		 * @internal param string $type mime type of provided $post ($post [$type] -> data [$mime] conversion will be done)
		 *
		 */
		public function post($url, $post, string $mime = null, string $target = null): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 * @param mixed $put used as a body
		 * @param string $mime used as a sent content type
		 *
		 * @param string $target
		 *
		 * @return IHttpHandler
		 * @internal param string $type
		 */
		public function put($url, $put, string $mime, string $target): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 * @param $delete
		 * @param string $mime
		 *
		 * @param string $target
		 *
		 * @return IHttpHandler
		 * @internal param string $type
		 */
		public function delete($url, $delete = null, string $mime = null, string $target = null): IHttpHandler;
	}
