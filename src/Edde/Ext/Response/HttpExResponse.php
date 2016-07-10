<?php
	namespace Edde\Ext\Response;

	use Edde\Api\Response\IResponse;
	use Edde\Common\Http\HttpResponse;

	/**
	 * Extended version of http response connected to application's response mechanism.
	 */
	class HttpExResponse extends HttpResponse implements IResponse {
		public function send() {
			foreach ($this->getHeaderList() as $header => $value) {
				header("$header: $value");
			}
			http_response_code($this->getCode());
			foreach ($this->getCookieList() as $cookie) {
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			$this->render();
		}
	}
