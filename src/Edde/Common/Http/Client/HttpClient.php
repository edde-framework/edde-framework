<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\Client\ClientException;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Http\Client\Event\DeleteEvent;
	use Edde\Common\Http\Client\Event\GetEvent;
	use Edde\Common\Http\Client\Event\HandlerEvent;
	use Edde\Common\Http\Client\Event\PatchEvent;
	use Edde\Common\Http\Client\Event\PostEvent;
	use Edde\Common\Http\Client\Event\PutEvent;
	use Edde\Common\Http\Client\Event\RequestEvent;
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
		use EventTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function get($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('GET');
			$this->event(new GetEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 */
		public function gete($url, string $target = null, string $mime = null) {
			$httpResponse = $this->get($url)
				->execute();
			return $target ? $httpResponse->body($target, $mime) : $httpResponse->getBody()
				->getBody();
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
		public function poste($url, IBody $body = null, string $target, string $mime = null) {
			$handler = $this->post($url);
			if ($body) {
				$handler->body($body);
			}
			return $handler->execute()
				->body($target, $mime);
		}

		/**
		 * @inheritdoc
		 */
		public function put($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('PUT');
			$this->event(new PutEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 */
		public function pute($url, IBody $body = null, string $target, string $mime = null) {
			$handler = $this->put($url);
			if ($body) {
				$handler->body($body);
			}
			return $handler->execute()
				->body($target, $mime);
		}

		/**
		 * @inheritdoc
		 */
		public function patch($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('PATCH');
			$this->event(new PatchEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
		}

		/**
		 * @inheritdoc
		 */
		public function patche($url, IBody $body = null, string $target, string $mime = null) {
			$handler = $this->patch($url);
			if ($body) {
				$handler->body($body);
			}
			return $handler->execute()
				->body($target, $mime);
		}

		/**
		 * @inheritdoc
		 */
		public function deletee($url, IBody $body = null, string $target, string $mime = null) {
			$handler = $this->delete($url);
			if ($body) {
				$handler->body($body);
			}
			return $handler->execute()
				->body($target, $mime);
		}

		/**
		 * @inheritdoc
		 */
		public function delete($url): IHttpHandler {
			$httpRequest = $this->createRequest($url)
				->setMethod('DELETE');
			$this->event(new DeleteEvent($httpRequest, $httpHandler = $this->request($httpRequest)));
			$this->event(new HandlerEvent($httpRequest, $httpHandler));
			return $httpHandler;
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
				CURLOPT_POST => strtoupper($method) === 'POST',
			]);
			$httpHandler = $this->container->create(HttpHandler::class, [
				$httpRequest,
				$curl,
			], __METHOD__);
			$httpHandler->chain($this);
			return $httpHandler;
		}

		/**
		 * @param IUrl|string $url
		 *
		 * @return HttpRequest
		 * @throws UrlException
		 */
		protected function createRequest($url) {
			$httpRequest = new HttpRequest(new HeaderList(), new CookieList());
			$httpRequest->setRequestUrl(RequestUrl::create($url));
			$this->event(new RequestEvent($httpRequest));
			return $httpRequest;
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
