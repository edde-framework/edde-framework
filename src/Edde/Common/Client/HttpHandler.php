<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client;

	use Edde\Api\Client\ClientException;
	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\AbstractObject;
	use Edde\Common\Http\Body;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Http\HttpUtils;

	/**
	 * Http client handler; this should not be used in common; only as a result from HttpClient calls
	 */
	class HttpHandler extends AbstractObject implements IHttpHandler, ILazyInject {
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var resource
		 */
		protected $curl;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IHttpRequest $httpRequest
		 * @param resource $curl
		 */
		public function __construct(IHttpRequest $httpRequest, $curl) {
			$this->httpRequest = $httpRequest;
			$this->curl = $curl;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function execute(): IHttpResponse {
			if ($this->curl === null) {
				throw new ClientException(sprintf('Cannot execute handler for the url [%s] more than once.', (string)$this->httpRequest->getRequestUrl()));
			}
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->httpRequest->getHeaderList()
				->headers());
			$headerList = new HeaderList();
			curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, function ($curl, $header) use ($headerList) {
				$length = strlen($header);
				if (($text = trim($header)) !== '' && strpos($header, ':') !== false) {
					list($header, $content) = explode(':', $header, 2);
					$headerList->set($header, trim($content));
				}
				return $length;
			});
			if (($content = curl_exec($this->curl)) === false) {
				$error = curl_error($this->curl);
				$errorCode = curl_errno($this->curl);
				curl_close($this->curl);
				$this->curl = null;
				throw new ClientException(sprintf('%s: %s', (string)$this->httpRequest->getRequestUrl(), $error), $errorCode);
			}
			if (is_string($contentType = $headerList->get('Content-Type', curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE)))) {
				$type = HttpUtils::contentType($contentType);
			}
			$headerList->set('Content-Type', $contentType);
			curl_close($this->curl);
			$this->curl = null;
			$this->container->inject($httpResponse = new HttpResponse($this->container->inject(new Body($content, isset($type) ? $type->type : $contentType))));
			$httpResponse->setHeaderList($headerList);
			return $httpResponse;
		}
	}
