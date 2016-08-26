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
		 * replace set of the given controls in a current page by control's ids
		 *
		 * @param IHtmlControl[] $controlList
		 *
		 * @return $this
		 * @throws ControlException
		 */
		public function replaceControlList(array $controlList) {
			$this->replaceControlList = [];
			foreach ($controlList as $selector => $control) {
				$this->addReplaceControl($control, is_string($selector) ? $selector : null);
			}
			return $this;
		}

		public function addReplaceControl(IHtmlControl $htmlControl, $selector = null) {
			if (($id = $htmlControl->getId()) === null && $selector === null) {
				throw new ControlException(sprintf('Cannot replace control [%s] without selector or preset control id.', get_class($htmlControl)));
			}
			$this->replaceControlList[$selector ?: '#' . $id] = $htmlControl;
			return $this;
		}

		/**
		 * add list of control which will be added under the specified selector/id (those controls can have duplicite id to be bound to the same parent)
		 *
		 * @param array $controlList
		 *
		 * @return $this
		 */
		public function addControlList(array $controlList) {
			$this->replaceControlList = [];
			foreach ($controlList as $selector => $control) {
				$this->addAddControl($control, is_string($selector) ? $selector : null);
			}
			return $this;
		}

		public function addAddControl(IHtmlControl $htmlControl, $selector = null) {
			if (($id = $htmlControl->getId()) === null && $selector === null) {
				throw new ControlException(sprintf('Cannot replace control [%s] without selector or preset control id.', get_class($htmlControl)));
			}
			$this->addControlList[$selector ?: '#' . $id] = $htmlControl;
			return $this;
		}

		public function send() {
			$response = [];
			$response['redirect'] = $this->redirect;
			if ($this->redirect === null) {
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
