<?php
	namespace Edde\Common\Control\Html;

	class PasswordInputControl extends AbstractHtmlValueControl {
		protected function onPrepare() {
			$this->setTag('input', false);
			$this->addClass('edde-text-input');
			$this->addAttributeList([
				'type' => 'password',
			]);
		}
	}
