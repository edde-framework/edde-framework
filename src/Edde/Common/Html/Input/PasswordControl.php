<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Input;

	use Edde\Common\Html\AbstractHtmlControl;

	class PasswordControl extends AbstractHtmlControl {
		public function setValue(string $value) {
			$this->setAttribute('value', $value);
			return $this;
		}

		public function setPlaceholder(string $value) {
			$this->setAttribute('placeholder', $value);
			return $this;
		}

		protected function handleInit() {
			parent::handleInit();
			$this->javascript(self::class)
				->setTag('input', false)
				->addAttributeList([
					'type' => 'password',
				]);
		}
	}
