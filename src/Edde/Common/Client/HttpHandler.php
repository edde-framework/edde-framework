<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client;

	use Edde\Api\Client\ClientException;
	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\AbstractObject;
	use Edde\Common\Http\Body;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Http\HttpUtils;
	use Edde\Common\Strings\StringException;

	/**
	 * Http client handler; this should not be used in common; only as a result from HttpClient calls
	 */
	class HttpHandler extends AbstractObject implements IHttpHandler, ILazyInject {
		use LazyContainerTrait;
		use LazyTempDirectoryTrait;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var resource
		 */
		protected $curl;
		/**
		 * cookie file; if set, cookies will be supported
		 *
		 * @var IFile
		 */
		protected $cookie;

		/**
		 * @param IHttpRequest $httpRequest
		 * @param resource $curl
		 */
		public function __construct(IHttpRequest $httpRequest, $curl) {
			$this->httpRequest = $httpRequest;
			$this->curl = $curl;
		}

		/**
		 * @inheritdoc
		 */
		public function authorization(string $authorization): IHttpHandler {
			$this->header('Authorization', $authorization);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function header(string $name, string $value): IHttpHandler {
			$this->httpRequest->getHeaderList()
				->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function keepConnectionAlive(): IHttpHandler {
			$this->header('Connection', 'keep-alive');
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function content($content, string $mime, string $target): IHttpHandler {
			$this->httpRequest->setBody($this->container->inject(new Body($content, $mime, $target)));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function body(IBody $body): IHttpHandler {
			$this->httpRequest->setBody($this->container->inject($body));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function cookie($file, bool $reset = false): IHttpHandler {
			$this->cookie = [
				is_string($file) ? $this->tempDirectory->file($file) : $file,
				$reset,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function agent(string $agent): IHttpHandler {
			curl_setopt($this->curl, CURLOPT_USERAGENT, $agent);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 * @throws StringException
		 */
		public function execute(): IHttpResponse {
			if ($this->curl === null) {
				throw new ClientException(sprintf('Cannot execute handler for the url [%s] more than once.', (string)$this->httpRequest->getRequestUrl()));
			}
			$options = [];
			if ($body = $this->httpRequest->getBody()) {
				$options[CURLOPT_POSTFIELDS] = $body->convert();
				if (($target = $body->getTarget()) !== '') {
					$this->header('Content-Type', $target);
				}
			}
			$postList = $this->httpRequest->getPostList();
			if ($postList->isEmpty() === false) {
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $postList->array();
			}
			if ($this->cookie) {
				/** @var $cookie IFile */
				list($cookie, $reset) = $this->cookie;
				$reset ? $cookie->delete() : null;
				$options[CURLOPT_COOKIEFILE] = $options[CURLOPT_COOKIEJAR] = $cookie->getPath();
			}
			$headerList = new HeaderList();
			$cookieList = new CookieList();
			/** @noinspection PhpUnusedParameterInspection */
			/** @noinspection PhpDocSignatureInspection */
			$options[CURLOPT_HEADERFUNCTION] = function ($curl, $header) use ($headerList, $cookieList) {
				$length = strlen($header);
				if (($text = trim($header)) !== '' && strpos($header, ':') !== false) {
					list($header, $content) = explode(':', $header, 2);
					$headerList->set($header, $content = trim($content));
					switch ($header) {
						case 'Set-Cookie':
							$cookieList->addCookie(HttpUtils::cookie($content));
							break;
					}
				}
				return $length;
			};
			$options[CURLOPT_HTTPHEADER] = $this->httpRequest->getHeaderList()
				->headers();
			curl_setopt_array($this->curl, $options);
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
			$this->container->inject($httpResponse = new HttpResponse($this->container->inject(new Body($content, isset($type) ? $type->mime : $contentType))));
			$httpResponse->setHeaderList($headerList);
			$httpResponse->setCookieList($cookieList);
			return $httpResponse;
		}
	}
