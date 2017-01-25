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
	use Edde\Api\Http\Client\IResponse;
	use Edde\Api\Http\IRequest;
	use Edde\Common\Converter\Content;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpUtils;
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
		 * @var IRequest
		 */
		protected $request;
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
		 * @param IRequest $request
		 * @param resource $curl
		 */
		public function __construct(IRequest $request, $curl) {
			$this->request = $request;
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
		public function basic(string $user, string $password): IHttpHandler {
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
		public function header(string $name, string $value): IHttpHandler {
			$this->request->header($name, $value);
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

		/**
		 * @inheritdoc
		 */
		public function content(IContent $content, array $targetList): IHttpHandler {
			$this->request->setContent($content);
			$this->targetList = $targetList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function contentType(string $contentType): IHttpHandler {
			$this->request->header('Content-Type', $contentType);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 * @throws StringException
		 */
		public function execute(): IResponse {
			if ($this->curl === null) {
				throw new ClientException(sprintf('Cannot execute handler for the url [%s] more than once.', (string)$this->request->getRequestUrl()));
			}
			$options = [];
			if ($content = $this->request->getContent()) {
				$convertable = $this->converterManager->content($content, $this->targetList);
				$options[CURLOPT_POSTFIELDS] = $convertable->convert();
				$headerList = $this->request->getHeaderList();
				if ($headerList->has('Content-Type') === false) {
					$this->header('Content-Type', $convertable->getTarget());
				}
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
			$options[CURLOPT_HTTPHEADER] = $this->request->getHeaderList()
				->headers();
			curl_setopt_array($this->curl, $options);
			if (($content = curl_exec($this->curl)) === false) {
				$error = curl_error($this->curl);
				$errorCode = curl_errno($this->curl);
				curl_close($this->curl);
				$this->curl = null;
				throw new ClientException(sprintf('%s: %s', (string)$this->request->getRequestUrl(), $error), $errorCode);
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
