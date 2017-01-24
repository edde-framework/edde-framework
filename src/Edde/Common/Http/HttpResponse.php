<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpResponse;

	class HttpResponse extends AbstractHttp implements IHttpResponse {
		/**
		 * @var int
		 */
		protected $code;

		public function __construct() {
			parent::__construct(new HeaderList(), new CookieList());
			$this->code = 200;
		}

		/**
		 * @inheritdoc
		 */
		public function setCode(int $code): IHttpResponse {
			$this->code = $code;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getCode(): int {
			return $this->code;
		}

		/**
		 * @inheritdoc
		 */
		public function redirect(string $redirect): IHttpResponse {
			$this->headerList->set('location', $redirect);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function body(string $target, $mime = null) {
			return $this->body->convert($target, $mime);
		}

		/**
		 * @inheritdoc
		 */
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
	}
