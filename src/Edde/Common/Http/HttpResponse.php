<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\AbstractObject;

	class HttpResponse extends AbstractObject implements IHttpResponse {
		/**
		 * @var int
		 */
		protected $code;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var ICookieList
		 */
		protected $cookieList;
		/**
		 * @var callable
		 */
		protected $renderCallback;

		public function __construct() {
			$this->code = 200;
			$this->headerList = new HeaderList();
			$this->cookieList = new CookieList();
			$this->renderCallback = function () {
				http_response_code($this->getCode());
				foreach ($this->getHeaderList() as $header => $value) {
					header("$header: $value");
				}
				foreach ($this->getCookieList() as $cookie) {
					setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
				}
			};
		}

		public function getCode() {
			return $this->code;
		}

		public function setCode($code) {
			$this->code = $code;
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

		public function setRenderCallback(callable $callback) {
			$this->renderCallback = $callback;
			return $this;
		}

		public function render() {
			call_user_func($this->renderCallback);
			return $this;
		}
	}
