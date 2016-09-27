<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client;

	use Edde\Api\Client\ClientException;
	use Edde\Api\Client\IHttpClient;
	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Http\IHttpRequest;
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
		use EventTrait;
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;
		/**
		 * @var IContainer
		 */
		protected $container;

		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function get($url): IHttpHandler {
			return $this->request($this->createRequest($url)
				->setMethod('GET'));
		}

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

		protected function createRequest($url) {
			$httpRequest = new HttpRequest(new PostList(), new HeaderList(), new CookieList());
			$httpRequest->setRequestUrl(RequestUrl::create($url));
			$this->event(new RequestEvent($httpRequest));
			return $httpRequest;
		}

		public function post($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('POST');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		public function put($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('PUT');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		public function delete($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('DELETE');
			$this->event(new PostEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		protected function prepare() {
			if (extension_loaded('curl') === false) {
				throw new ClientException('Curl extension is not loaded in PHP.');
			}
		}
	}
