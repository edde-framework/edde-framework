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

		public function __construct() {
			$this->code = 200;
			$this->headerList = new HeaderList();
			$this->cookieList = new CookieList();
		}

		public function send(): IHttpResponse {
			http_response_code($this->getCode());
			foreach ($this->getHeaderList() as $header => $value) {
				header("$header: $value");
			}
			foreach ($this->getCookieList() as $cookie) {
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			return $this;
		}

		public function getCode(): int {
			return $this->code;
		}

		public function setCode(int $code): IHttpResponse {
			$this->code = $code;
			return $this;
		}

		public function getHeaderList(): IHeaderList {
			return $this->headerList;
		}

		public function setHeaderList(IHeaderList $headerList): IHttpResponse {
			$this->headerList = $headerList;
			return $this;
		}

		public function getCookieList(): ICookieList {
			return $this->cookieList;
		}

		public function setCookieList(ICookieList $cookieList): IHttpResponse {
			$this->cookieList = $cookieList;
			return $this;
		}

		public function redirect(string $redirect): IHttpResponse {
			$this->headerList->set('location', $redirect);
			return $this;
		}

		public function contentType(string $contentType): IHttpResponse {
			$this->headerList->set('Content-Type', $contentType);
			return $this;
		}
	}
