<?php
	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\AbstractObject;

	class HttpResponse extends AbstractObject implements IHttpResponse {
		/**
		 * @var int
		 */
		private $code;
		/**
		 * @var IHeaderList
		 */
		private $headerList;
		/**
		 * @var ICookieList
		 */
		private $cookieList;
		/**
		 * @var callable
		 */
		private $renderCallback;

		/**
		 * @param int $code
		 * @param IHeaderList $headerList
		 * @param ICookieList $cookieList
		 * @param callable $renderCallback
		 */
		public function __construct($code, IHeaderList $headerList, ICookieList $cookieList, callable $renderCallback) {
			$this->code = $code;
			$this->headerList = $headerList;
			$this->cookieList = $cookieList;
			$this->renderCallback = $renderCallback;
		}

		public function getCode() {
			return $this->code;
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

		public function render() {
			call_user_func($this->renderCallback);
			return $this;
		}
	}
