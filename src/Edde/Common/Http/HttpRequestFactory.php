<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Url\Url;

	/**
	 * Factory for creating IHttpRequests from http input.
	 */
	class HttpRequestFactory extends AbstractObject implements IHttpRequestFactory {
		public function create() {
			$url = Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			$httpRequest = new HttpRequest(PostList::create($_POST), new HeaderList(), CookieList::create($_COOKIE));
			return $httpRequest->setUrl($url)
				->setMethod(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null)
				->setRemoteAddress(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null)
				->setRemoteHost(isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null)
				->setBody(function () {
					return file_get_contents('php://input');
				});
		}
	}
