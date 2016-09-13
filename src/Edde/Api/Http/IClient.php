<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	use Edde\Api\Url\IUrl;
	use Edde\Api\Usable\IUsable;

	interface IClient extends IUsable {
		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpResponse
		 */
		public function get($url): IHttpResponse;

		/**
		 * @param string|IUrl $url
		 * @param IPostList|mixed $post
		 * @param string|null $mime
		 *
		 * @return IHttpResponse
		 */
		public function post($url, $post, string $mime = null): IHttpResponse;

		/**
		 * @param string|IUrl $url
		 * @param mixed $put used as a body
		 * @param string $mime used as a sent content type
		 *
		 * @return IHttpResponse
		 */
		public function put($url, $put, string $mime): IHttpResponse;

		/**
		 * @param string|IUrl $url
		 * @param $delete
		 * @param string $mime
		 *
		 * @return IHttpResponse
		 */
		public function delete($url, $delete = null, string $mime = null): IHttpResponse;
	}
