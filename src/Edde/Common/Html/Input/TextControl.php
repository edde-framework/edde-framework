<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Input;

	use Edde\Common\Html\AbstractHtmlControl;

	class TextControl extends AbstractHtmlControl {
		protected function prepare() {
			parent::prepare()
				->setTag('input', false)
				->addAttributeList([
					'type' => 'text',
					'value' => $this->node->getValue(),
				])
				->client();
		}
	}
