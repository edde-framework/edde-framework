<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Url\IUrl;
	use Edde\Common\AbstractObject;
	use Edde\Common\Url\Url;

	class HttpRequest extends AbstractObject implements IHttpRequest {
		/**
		 * @var IUrl
		 */
		protected $url;
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var IPostList
		 */
		protected $postList;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var ICookieList
		 */
		protected $cookieList;
		/**
		 * @var string|null
		 */
		protected $remoteAddress;
		/**
		 * @var string|null
		 */
		protected $remoteHost;
		protected $body;
		protected $hasBody = false;
		/**
		 * @var IHttpResponse
		 */
		protected $response;
		/**
		 * @var IUrl
		 */
		protected $referer;

		/**
		 * @param IPostList $postList
		 * @param IHeaderList $headerList
		 * @param ICookieList $cookieList
		 */
		public function __construct(IPostList $postList, IHeaderList $headerList, ICookieList $cookieList) {
			$this->postList = $postList;
			$this->headerList = $headerList;
			$this->cookieList = $cookieList;
		}

		public function getUrl() {
			return $this->url;
		}

		public function setUrl(IUrl $url) {
			$this->url = $url;
			return $this;
		}

		public function getMethod() {
			return $this->method;
		}

		public function setMethod($method) {
			$this->method = $method;
			return $this;
		}

		public function isMethod($method) {
			return strcasecmp($this->method, $method) === 0;
		}

		public function getPostList() {
			return $this->postList;
		}

		public function setPostList(IPostList $postList) {
			$this->postList = $postList;
			return $this;
		}

		public function getHeaderList() {
			return $this->headerList;
		}

		public function setHeaderList(IHeaderList $headerList) {
			$this->headerList = $headerList;
			return $this;
		}

		public function getCookieList() {
			return $this->cookieList;
		}

		public function setCookieList(ICookieList $cookieList) {
			$this->cookieList = $cookieList;
			return $this;
		}

		public function getRemoteAddress() {
			return $this->remoteAddress;
		}

		public function setRemoteAddress($remoteAddress) {
			$this->remoteAddress = $remoteAddress;
			return $this;
		}

		public function getRemoteHost() {
			if ($this->remoteHost === null && $this->remoteAddress !== null) {
				$this->remoteHost = gethostbyaddr($this->remoteAddress);
			}
			return $this->remoteHost;
		}

		public function setRemoteHost($remoteHost) {
			$this->remoteHost = $remoteHost;
			return $this;
		}

		public function getReferer() {
			if ($this->referer === null && $this->headerList->has('referer')) {
				$this->referer = new Url($this->headerList->get('referer'));
			}
			return $this->referer;
		}

		public function isSecured() {
			return $this->url->getScheme() === 'https';
		}

		public function isAjax() {
			return $this->headerList->get('X-Requested-With') === 'XMLHttpRequest';
		}

		public function getBody() {
			if ($this->hasBody === false) {
				$this->hasBody = true;
				$this->body = is_callable($this->body) ? call_user_func($this->body) : $this->body;
			}
			return $this->body;
		}

		public function setBody($body) {
			$this->body = $body;
			return $this;
		}

		public function getResponse() {
			return $this->response;
		}

		public function setResponse(IHttpResponse $httpResponse) {
			$this->response = $httpResponse;
			return $this;
		}
	}
