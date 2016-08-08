<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Html\IHtmlControl;

	class ButtonControl extends AbstractHtmlControl {
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

		public function setTitle($title) {
			$this->usse();
			$this->node->setValue($title);
			return $this;
		}

		public function setHint($hint) {
			$this->setAttribute('title', $hint);
			return $this;
		}

		public function setAction($control, $action) {
			$this->addAttributeList([
				'data-control' => $control,
				'data-action' => $action,
			]);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('div', true);
			$this->addClass('button edde-clickable');
		}
	}
