<?php
	namespace Edde\Common\Control\Html;

	class TextInputControl extends AbstractHtmlValueControl {
		protected function onPrepare() {
			$this->setTag('input', false);
			$this->addClass('edde-text-input');
			$this->addAttributeList([
				'type' => 'text',
				'value' => $this->node->getValue(),
			]);
		}
	}
