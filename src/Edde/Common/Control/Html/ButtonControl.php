<?php
	namespace Edde\Common\Control\Html;

	class ButtonControl extends AbstractHtmlControl {
		/**
		 * @var string
		 */
		protected $title;
		/**
		 * @var string
		 */
		protected $control;
		/**
		 * @var string
		 */
		protected $action;
		/**
		 * @var string|null
		 */
		protected $hint;

		/**
		 * @param string $title
		 * @param string $control
		 * @param string $action
		 * @param null|string $hint
		 */
		public function __construct($title, $control, $action, $hint = null) {
			$this->title = $title;
			$this->control = $control;
			$this->action = $action;
			$this->hint = $hint;
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('button', true);
			$this->setAttribute('data-control', $this->control);
			$this->setAttribute('data-action', $this->action);
			if ($this->hint !== null) {
				$this->setAttribute('title', $this->hint);
			}
			$this->addClass('button edde-clickable');
			$this->node->setValue($this->title);
		}

		protected function onPrepare() {
		}
	}
