<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	use Edde\Api\Http\IHttpResponse;

	/**
	 * Simple html content response.
	 */
	class HtmlResponse extends AbstractResponse {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var callable
		 */
		protected $callback;

		/**
		 * @param IHttpResponse $httpResponse
		 */
		public function __construct(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function send() {
			echo call_user_func($this->callback);
		}

		public function render(callable $callback) {
			$this->callback = $callback;
			$this->httpResponse->getHeaderList()
				->set('Content-Type', 'text/html');
			$this->httpResponse->setResponse($this);
			$this->httpResponse->render();
			return $this;
		}
	}
