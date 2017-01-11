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
			return $this->head;
		}

		/**
		 * return body control of a html document
		 *
		 * @return BodyControl
		 */
		public function getBody() {
			return $this->body;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'html';
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function render(int $indent = 0): string {
			return "<!DOCTYPE html>\n" . parent::render($indent);
		}

		/**
		 * this is shortcut method for adding a new control to the body of document
		 *
		 * @inheritdoc
		 */
		public function addControl(IControl $control): IControl {
			$this->body->addControl($control);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			parent::addControl($this->head = $this->createControl(HeadControl::class));
			parent::addControl($this->body = $this->createControl(BodyControl::class));
		}
	}
