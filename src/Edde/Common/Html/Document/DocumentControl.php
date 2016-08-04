<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Api\Control\IControl;
	use Edde\Common\Html\AbstractHtmlControl;

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
			parent::addControl($this->head = $this->createControl(HeadControl::class));
			parent::addControl($this->body = $this->createControl(BodyControl::class));
		}
	}
