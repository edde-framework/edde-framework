<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\Client\ClientException;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\RequestUrl;
	use Edde\Common\Object;

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
		public function request(IHttpRequest $httpRequest): IHttpHandler {
			curl_setopt_array($curl = curl_init($url = (string)$httpRequest->getRequestUrl()), [
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_FAILONERROR => true,
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => 'utf-8',
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => $method = $httpRequest->getMethod(),
				CURLOPT_POST => $method === 'POST',
			]);
			return $this->container->create(HttpHandler::class, [
				$httpRequest,
				$curl,
			], __METHOD__);
		}

		/**
		 * @param IRequestUrl|string $url
		 * @param string             $method
		 *
		 * @return HttpRequest
		 */
		protected function createRequest($url, string $method) {
			return $this->container->create(HttpRequest::class, [
				RequestUrl::create($url),
				new HeaderList(),
				new CookieList(),
			])
				->setMethod($method);
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
