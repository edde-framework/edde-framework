<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	use Edde\Api\Control\ControlException;
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
		protected $replaceControlList = [];
		/**
		 * @var IHtmlControl[]
		 */
		protected $addControlList = [];
		protected $javaScriptList = [];
		protected $styleSheetList = [];

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

		public function setJavaScriptList(array $javaScriptList) {
			$this->javaScriptList = $javaScriptList;
			return $this;
		}

		public function setStyleSheetList(array $styleSheetList) {
			$this->styleSheetList = $styleSheetList;
			return $this;
		}

		/**
		 * replace set of the given controls in a current page by control's ids
		 *
		 * @param IHtmlControl[] $controlList
		 *
		 * @return $this
		 * @throws ControlException
		 */
		public function replace(IHtmlControl ...$controlList) {
			$this->replaceControlList = [];
			foreach ($controlList as $selector => $control) {
				$selector = is_string($selector) ? $selector : null;
				if (($id = $control->getId()) === '' && $selector === null) {
					throw new ControlException(sprintf('Cannot replace control [%s] without selector or preset control id.', get_class($control)));
				}
				$this->replaceControlList[$selector ?: '#' . $id] = $control;
			}
			return $this;
		}

		/**
		 * add list of control which will be added under the specified selector/id
		 *
		 * @param IHtmlControl[] $controlList
		 *
		 * @return $this
		 * @throws ControlException
		 */
		public function add(IHtmlControl ...$controlList) {
			$this->replaceControlList = [];
			foreach ($controlList as $selector => $control) {
				$selector = is_string($selector) ? $selector : null;
				if (($id = $control->getId()) === '' && $selector === null) {
					throw new ControlException(sprintf('Cannot replace control [%s] without selector or preset control id.', get_class($control)));
				}
				$this->addControlList[$selector ?: '#' . $id] = $control;
			}
			return $this;
		}

		public function send() {
			$response = [];
			$response['redirect'] = $this->redirect;
			if ($this->redirect === null) {
				$response['javaScript'] = $this->javaScriptList;
				$response['styleSheet'] = $this->styleSheetList;
				foreach ($this->replaceControlList as $selector => $control) {
					$response['selector'][$selector] = [
						'action' => 'replace',
						'source' => $control->render(),
					];
				}
				foreach ($this->addControlList as $selector => $control) {
					$response['selector'][$selector] = [
						'action' => 'add',
						'source' => $control->render(),
					];
				}
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
