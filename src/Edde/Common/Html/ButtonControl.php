<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Html\IHtmlControl;

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

		/**
		 * bind action to the specific control; all data from that control will be sent to the action
		 *
		 * @param IHtmlControl $htmlControl
		 *
		 * @return $this
		 * @throws ControlException
		 */
		public function bind(IHtmlControl $htmlControl) {
			if (($id = $htmlControl->getId()) === '') {
				throw new ControlException(sprintf('Cannot bind [%s] to [%s] because target id is empty.', static::class, get_class($htmlControl)));
			}
			$this->setAttribute('data-bind', $id);
			return $this;
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
	}
