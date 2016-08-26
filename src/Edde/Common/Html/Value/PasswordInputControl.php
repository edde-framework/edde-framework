<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Value;

	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class PasswordInputControl extends HtmlValueControl {
		static public function macro(): IMacro {
			return new ControlMacro('password', static::class);
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('input', false)
				->addClass('edde-text-input')
				->addAttributeList([
					'type' => 'password',
				]);
		}
	}
