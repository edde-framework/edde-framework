<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Url\Url;

	/**
	 * Factory for creating IHttpRequests from http input.
	 */
	class HttpRequestFactory extends AbstractObject implements IHttpRequestFactory {
		public function create() {
			$url = Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			$httpRequest = new HttpRequest(PostList::create($_POST), $this->createHeaderList(), CookieList::create($_COOKIE));
			return $httpRequest->setUrl($url)
				->setMethod($_SERVER['REQUEST_METHOD'] ?? null)
				->setRemoteAddress($_SERVER['REMOTE_ADDR'] ?? null)
				->setRemoteHost($_SERVER['REMOTE_HOST'] ?? null)
				->setBody(function () {
					return file_get_contents('php://input');
				});
		}

		protected function createHeaderList(): IHeaderList {
			$headers = [];
			$copy_server = [
				'CONTENT_TYPE' => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5' => 'Content-Md5',
			];
			foreach ($_SERVER as $key => $value) {
				if (strpos($key, 'HTTP_') === 0) {
					$key = substr($key, 5);
					if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers[$key] = $value;
					}
				} elseif (isset($copy_server[$key])) {
					$headers[$copy_server[$key]] = $value;
				}
			}
			if (!isset($headers['Authorization'])) {
				if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
					$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				} elseif (isset($_SERVER['PHP_AUTH_USER'])) {
					$basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
					$headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
				} elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
					$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
				}
			}
			return (new HeaderList())->put($headers);
		}
	}
