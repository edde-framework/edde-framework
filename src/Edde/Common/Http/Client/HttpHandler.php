<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Http\Client\ClientException;
	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IResponse;
	use Edde\Common\Converter\Content;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpUtils;
	use Edde\Common\Http\Response;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringException;

	/**
	 * Http client handler; this should not be used in common; only as a result from HttpClient calls
	 */
	class HttpHandler extends Object implements IHttpHandler {
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
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
		 * @var array
		 */
		protected $targetList;

		/**
		 * @param IHttpRequest $httpRequest
		 * @param resource     $curl
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
		public function basic(string $user, string $password): \Edde\Api\Http\Client\IHttpHandler {
			curl_setopt_array($this->curl, [
				CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
				CURLOPT_USERPWD => vsprintf('%s:%s', func_get_args()),
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function digest(string $user, string $password): IHttpHandler {
			curl_setopt_array($this->curl, [
				CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
				CURLOPT_USERPWD => vsprintf('%s:%s', func_get_args()),
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function header(string $name, string $value): \Edde\Api\Http\Client\IHttpHandler {
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

		public function content(IContent $content, array $targetList): IHttpHandler {
			$this->httpRequest->setContent($content);
			$this->targetList = $targetList;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 * @throws StringException
		 */
		public function execute(): IResponse {
			if ($this->curl === null) {
				throw new ClientException(sprintf('Cannot execute handler for the url [%s] more than once.', (string)$this->httpRequest->getRequestUrl()));
			}
			$options = [];
			if ($content = $this->httpRequest->getContent()) {
				$convertable = $this->converterManager->content($content, $this->targetList);
				$options[CURLOPT_POSTFIELDS] = $convertable->convert();
				$this->header('Content-Type', $convertable->getTarget());
			}
//			$postList = $this->httpRequest->getPostList();
//			$options[CURLOPT_POST] = false;
//			if ($postList->isEmpty() === false) {
//				$options[CURLOPT_POST] = true;
//				$options[CURLOPT_POSTFIELDS] = $postList->array();
//			}
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
				$type = HttpUtils::contentType((string)$contentType);
			}
			$headerList->set('Content-Type', $contentType);
			$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
			curl_close($this->curl);
			$this->curl = null;
			/** @var $response IResponse */
			$response = $this->container->create(Response::class, [
				$code,
				$headerList,
				$cookieList,
			], __METHOD__);
			$response->setContent($this->container->create(Content::class, [
				$content,
				isset($type) ? $type->mime : $contentType,
			], __METHOD__));
			return $response;
		}
	}
