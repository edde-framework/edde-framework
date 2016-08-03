<?php
	namespace Edde\Common\Control\Html;

	class TextInputControl extends AbstractHtmlValueControl {
		protected function prepare() {
			parent::prepare();
			$this->setTag('input', false)
				->addClass('edde-text-input')
				->addAttributeList([
					'type' => 'text',
					'value' => $this->node->getValue(),
				]);
		}
	}
