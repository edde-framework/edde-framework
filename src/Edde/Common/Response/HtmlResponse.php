<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	use Edde\Api\Html\IHtmlControl;

	/**
	 * This response is useful for a response to the html page; there must be "something" listening to the specific data of this
	 * response.
	 */
	class HtmlResponse extends JsonHttpResponse {
		/**
		 * @var string
		 */
		protected $redirect;
		/**
		 * @var IHtmlControl[]
		 */
		protected $controlList = [];

		public function __construct() {
			parent::__construct(null);
		}

		public function redirect($redirect) {
			$this->redirect = $redirect;
			return $this;
		}

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

		public function render() {
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
			$this->json = $response;
			parent::render();
		}
	}
