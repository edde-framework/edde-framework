<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Http\IHttpResponse;

	/**
	 * This response is useful for a response to the html page; there must be "something" listening to the specific data of this
	 * response.
	 */
	class AjaxResponse extends AbstractResponse {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var string
		 */
		protected $redirect;
		/**
		 * @var IHtmlControl[]
		 */
		protected $controlList = [];

		/**
		 * @param IHttpResponse $httpResponse
		 */
		public function __construct(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function redirect($redirect) {
			$this->redirect = $redirect;
			return $this;
		}

		/**
		 * @param IHtmlControl[] $controlList
		 *
		 * @return $this
		 */
		public function setControlList(array $controlList) {
			$this->controlList = [];
			foreach ($controlList as $selector => $control) {
				$this->addControl($control, is_string($selector) ? $selector : null);
			}
			return $this;
		}

		public function addControl(IHtmlControl $htmlControl, $selector = null) {
			$this->controlList[$selector ?: '#' . $htmlControl->getId()] = $htmlControl;
			return $this;
		}

		public function send() {
			$response = [];
			foreach ($this->controlList as $selector => $control) {
				$response['selector'][$selector] = [
					'action' => 'replace',
					'source' => $control->render(),
				];
			}
			if ($this->redirect !== null) {
				$response['redirect'] = $this->redirect;
			}
			echo json_encode($response);
		}

		public function render() {
			$this->httpResponse->contentType('application/json');
			$this->httpResponse->setResponse($this);
			$this->httpResponse->render();
			return $this;
		}
	}
