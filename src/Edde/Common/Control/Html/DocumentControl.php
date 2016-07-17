<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\IControl;

	/**
	 * General control describing whole html document; this is some sort of representation of a DOM tree.
	 */
	class DocumentControl extends AbstractHtmlControl {
		/**
		 * @var HeadControl
		 */
		protected $head;
		/**
		 * @var BodyControl
		 */
		protected $body;

		/**
		 * return head control (head tag for a html document)
		 *
		 * @return HeadControl
		 */
		public function getHead() {
			$this->usse();
			return $this->head;
		}

		/**
		 * return body control of a html document
		 *
		 * @return BodyControl
		 */
		public function getBody() {
			$this->usse();
			return $this->body;
		}

		public function getTag() {
			return 'html';
		}

		public function render() {
			$this->usse();
			return "<!DOCTYPE html>\n" . parent::render();
		}

		/**
		 * this is shortcut method for adding a new control to the body of document
		 *
		 * @param IControl $control
		 *
		 * @return $this
		 */
		public function addControl(IControl $control) {
			$this->usse();
			$this->body->addControl($control);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			parent::addControl($this->head = new HeadControl());
			parent::addControl($this->body = new BodyControl());
		}

		protected function onPrepare() {
		}
	}
