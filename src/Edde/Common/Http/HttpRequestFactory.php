<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Common\Object;

	/**
	 * Factory for creating IHttpRequests from http input.
	 */
	class HttpRequestFactory extends Object {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function create(): IHttpRequest {

//			return (new HttpRequest(PostList::create($_POST), $headerList = $this->createHeaderList(), CookieList::create($_COOKIE)))->setRequestUrl(RequestUrl::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))
//				->setMethod($_SERVER['REQUEST_METHOD'] ?? '')
//				->setRemoteAddress($_SERVER['REMOTE_ADDR'] ?? '')
//				->setRemoteHost($_SERVER['REMOTE_HOST'] ?? '')
//				->setBody($this->container->create(Body::class, function () {
//					return file_get_contents('php://input');
//				}, (string)$headerList->getContentType()),));
		}
	}
