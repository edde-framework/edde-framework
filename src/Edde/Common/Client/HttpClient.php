<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client;

	use Edde\Api\Client\ClientException;
	use Edde\Api\Client\IHttpClient;
	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Client\Event\HandlerEvent;
	use Edde\Common\Client\Event\PostEvent;
	use Edde\Common\Client\Event\RequestEvent;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\PostList;
	use Edde\Common\Http\RequestUrl;

	/**
	 * Simple http client implementation.
	 */
	class HttpClient extends AbstractDeffered implements IHttpClient {
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
		use EventTrait;

		/**
		 * @inheritdoc
		 */
		public function get($url): IHttpHandler {
			return $this->request($this->createRequest($url)
				->setMethod('GET'));
		}

		/**
		 * @inheritdoc
		 */
		public function request(IHttpRequest $httpRequest): IHttpHandler {
			$this->use();
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
				CURLOPT_POST => strtoupper($method) === 'POST',
			]);
			return $this->container->inject(new HttpHandler($httpRequest, $curl));
		}

		/**
		 * @param IUrl|string $url
		 *
		 * @return HttpRequest
		 */
		protected function createRequest($url) {
			$httpRequest = new HttpRequest(new PostList(), new HeaderList(), new CookieList());
			$httpRequest->setRequestUrl(RequestUrl::create($url));
			$this->event(new RequestEvent($httpRequest));
			return $httpRequest;
		}

		/**
		 * @inheritdoc
		 */
		public function post($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('POST');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 */
		public function put($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('PUT');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 */
		public function delete($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('DELETE');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 */
		protected function prepare() {
			if (extension_loaded('curl') === false) {
				throw new ClientException('Curl extension is not loaded in PHP.');
			}
		}
	}
