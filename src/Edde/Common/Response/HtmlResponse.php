<?php
	namespace Edde\Common\Response;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Common\Http\HttpResponse;

	class HtmlResponse extends AbstractResponse {
		/**
		 * @var IHtmlControl[]
		 */
		protected $controlList = [];

		public function addControl($selector, IHtmlControl $htmlControl) {
			$this->controlList[$selector] = $htmlControl;
			return $this;
		}

		public function send() {
			$httpResponse = new HttpResponse();
			$httpResponse->getHeaderList()
				->set('Content-Type', 'application/json');
			$httpResponse->render();
			$response = [];
			foreach ($this->controlList as $selector => $control) {
				$response[$selector] = [
					'action' => 'replace',
					'source' => $control->render(),
				];
			}
			echo json_encode($response);
		}
	}
