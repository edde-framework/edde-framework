<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IResponse;

	abstract class Response extends AbstractHttp implements IResponse {
		/**
		 * @var int
		 */
		protected $code;

		public function __construct(int $code, IHeaderList $headerList, ICookieList $cookieList) {
			parent::__construct($headerList, $cookieList);
			$this->code = $code;
		}

		/**
		 * @inheritdoc
		 */
		public function setCode(int $code): IResponse {
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
		public function redirect(string $redirect): IResponse {
			$this->headerList->set('location', $redirect);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function send(): IResponse {
			http_response_code($this->getCode());
			foreach ($this->getHeaderList() as $header => $value) {
				header("$header: $value");
			}
			/** @var $cookie ICookie */
			foreach ($this->getCookieList() as $cookie) {
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			return $this;
		}
	}
