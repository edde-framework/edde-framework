<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Input;

	use Edde\Common\Html\AbstractHtmlControl;

	class PasswordControl extends AbstractHtmlControl {
		protected function prepare() {
			parent::prepare();
			$this->setTag('input', false)
				->addAttributeList([
					'type' => 'password',
				]);
		}
	}
