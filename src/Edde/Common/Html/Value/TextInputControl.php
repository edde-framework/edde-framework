<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Value;

	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class TextInputControl extends HtmlValueControl {
		static public function macro(): IMacro {
			return new ControlMacro('text', static::class);
		}

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
