<?php
	namespace Edde\Common\Response;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Common\Http\HttpResponse;

	class HtmlResponse extends AbstractResponse {
		/**
		 * @var string
		 */
		protected $redirect;
		/**
		 * @var IHtmlControl[]
		 */
		protected $controlList = [];

		public function redirect($redirect) {
			$this->redirect = $redirect;
			return $this;
		}

		public function addControl($selector, IHtmlControl $htmlControl) {
			$this->controlList[$selector] = $htmlControl;
			return $this;
		}

		public function send() {
			$httpResponse = new HttpResponse();
			$headerList = $httpResponse->getHeaderList();
			$headerList->set('Content-Type', 'application/json');
			$httpResponse->render();
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
	}
