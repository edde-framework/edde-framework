<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IBody;
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
		 * @var IBody
		 */
		protected $body;

		public function __construct(IBody $body) {
			$this->code = 200;
			$this->headerList = new HeaderList();
			$this->cookieList = new CookieList();
			$this->body = $body;
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

		public function header(string $header, string $value): IHttpResponse {
			$this->headerList->set($header, $value);
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

		public function getBody(): IBody {
			return $this->body;
		}

		public function body(string $target, $mime = null) {
			return $this->body->convert($target, $mime);
		}
	}
