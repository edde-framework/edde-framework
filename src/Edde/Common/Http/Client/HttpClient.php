<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\Client\ClientException;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IRequest;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\Request;
	use Edde\Common\Http\RequestUrl;
	use Edde\Common\Object;
	use Edde\Common\Url\Url;

	/**
	 * Simple http client implementation.
	 */
	class HttpClient extends Object implements IHttpClient {
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function get($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function post($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function put($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function patch($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function delete($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function head($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function touch($url, $method = 'HEAD', array $headerList = []): IHttpClient {
			$url = Url::create($url);
			fwrite($handle = stream_socket_client('tcp://' . ($host = $url->getHost()) . ':' . $url->getPort(), $_, $_, 0, STREAM_CLIENT_ASYNC_CONNECT), implode("\r\n", array_merge([
					'HEAD ' . $url->getPath() . ' HTTP/1.1',
					'Host: ' . $host,
				], $headerList)) . "\r\n\r\n");
			fclose($handle);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function request(IRequest $request): IHttpHandler {
			curl_setopt_array($curl = curl_init($url = (string)$request->getRequestUrl()), [
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_FAILONERROR    => true,
				CURLOPT_FORBID_REUSE   => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => 'utf-8',
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT        => 60,
				CURLOPT_CUSTOMREQUEST  => $method = $request->getMethod(),
				CURLOPT_POST           => $method === 'POST',
			]);
			return $this->container->create(HttpHandler::class, [
				$request,
				$curl,
			], __METHOD__);
		}

		/**
		 * @param IRequestUrl|string $url
		 * @param string             $method
		 *
		 * @return IRequest
		 * @throws UrlException
		 */
		protected function createRequest($url, string $method): IRequest {
			/** @var $request Request */
			$request = $this->container->create(Request::class, [
				RequestUrl::create($url),
				new HeaderList(),
				new CookieList(),
			]);
			$request->setMethod($method);
			return $request;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 */
		protected function handleInit() {
			parent::handleInit();
			if (extension_loaded('curl') === false) {
				throw new ClientException('Curl extension is not loaded in PHP.');
			}
		}
	}
