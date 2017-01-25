<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\IHttpResponse;

	class HttpResponse extends Response implements IHttpResponse {
		/**
		 * @inheritdoc
		 */
		public function send(): IHttpResponse {
			http_response_code($this->getCode());
			foreach ($this->getHeaderList() as $header => $value) {
				header("$header: $value");
			}
			/** @var $cookie ICookie */
			foreach ($this->getCookieList() as $cookie) {
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			if ($this->content) {
				$this->headerList->has('Content-Type') ? null : header('Content-Type: ' . $this->content->getMime());
				echo $this->content->getContent();
			}
			return $this;
		}

		static public function createHttpResponse(): IHttpResponse {
			return new self(200, new HeaderList(), new CookieList());
		}
	}
