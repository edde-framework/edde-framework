<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Value;

	class TextInputControl extends HtmlValueControl {
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
