<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Input;

	use Edde\Common\Html\AbstractHtmlControl;

	class PasswordControl extends AbstractHtmlControl {
		protected function prepare() {
			parent::prepare()
				->javascript(self::class)
				->setTag('input', false)
				->addAttributeList([
					'type' => 'password',
				]);
		}
	}
