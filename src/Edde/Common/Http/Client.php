<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Http\ClientException;
	use Edde\Api\Http\IClient;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * Simple http client implementation.
	 */
	class Client extends AbstractUsable implements IClient {
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

		public function get($url): IHttpResponse {
			return $this->request($this->createRequest($url)
				->setMethod('GET'));
		}

		public function request(IHttpRequest $httpRequest): IHttpResponse {
			$this->use();
			$postList = $httpRequest->getPostList();
			$headerList = $httpRequest->getHeaderList();
			$body = $httpRequest->getBody();
			if (($mime = $body->getMime()) !== '') {
				$headerList->set('Content-Type', $mime);
			}
			$responseHeaderList = new HeaderList();
			curl_setopt_array($curl = curl_init($url = (string)$httpRequest->getRequestUrl()), [
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_FAILONERROR => true,
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => 'utf-8',
				CURLOPT_HTTPHEADER => $headerList->headers(),
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => ($method = $httpRequest->getMethod()),
				CURLOPT_POST => strtoupper($method) === 'POST',
				CURLOPT_POSTFIELDS => ($postList->isEmpty() ? $body->getBody() : $postList->array()),
				CURLOPT_HEADERFUNCTION => function ($curl, $header) use ($responseHeaderList) {
					$length = strlen($header);
					if (($text = trim($header)) !== '' && strpos($header, ':') !== false) {
						list($header, $content) = explode(':', $header, 2);
						$responseHeaderList->set($header, trim($content));
					}
					return $length;
				},
			]);
			if (($content = curl_exec($curl)) === false) {
				$error = curl_error($curl);
				$errorCode = curl_errno($curl);
				curl_close($curl);
				$curl = null;
				throw new ClientException(sprintf('%s: %s', $url, $error), $errorCode);
			}
			$headerList->set('Content-Type', $contentType = $headerList->get('Content-Type', curl_getinfo($curl, CURLINFO_CONTENT_TYPE)));
			curl_close($curl);
			$curl = null;
			$httpResponse = new HttpResponse($this->container->inject(new Body($content, $contentType)));
			$httpResponse->setHeaderList($headerList);
			return $httpResponse;
		}

		protected function createRequest($url) {
			$httpRequest = new HttpRequest(new PostList(), new HeaderList(), new CookieList());
			$httpRequest->setBody(new Body());
			$httpRequest->setRequestUrl(RequestUrl::create($url));
			return $httpRequest;
		}

		public function post($url, $post, string $mime = null): IHttpResponse {
			$httpRequest = $this->createRequest($url)
				->setMethod('POST');
			if ($post instanceof IPostList) {
				$httpRequest->setPostList($post);
			} else {
				$httpRequest->setBody(new Body($post, $mime));
			}
			return $this->request($httpRequest);
		}

		public function put($url, $put, string $mime): IHttpResponse {
			return $this->request($this->createRequest($url)
				->setMethod('PUT')
				->setBody(new Body($put, $mime)));
		}

		public function delete($url, $delete = null, string $mime = null): IHttpResponse {
			$request = $this->createRequest($url)
				->setMethod('DELETE');
			if ($delete !== null) {
				$request->setBody(new Body($delete, $mime));
			}
			return $this->request($request);
		}

		protected function prepare() {
			if (extension_loaded('curl') === false) {
				throw new ClientException('Curl extension is not loaded in PHP.');
			}
		}
	}
