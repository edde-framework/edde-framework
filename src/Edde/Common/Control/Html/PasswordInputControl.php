<?php
	namespace Edde\Common\Control\Html;

	class PasswordInputControl extends AbstractHtmlValueControl {
		protected function prepare() {
			parent::prepare();
			$this->setTag('input', false)
				->addClass('edde-text-input')
				->addAttributeList([
					'type' => 'password',
				]);
		}
	}
