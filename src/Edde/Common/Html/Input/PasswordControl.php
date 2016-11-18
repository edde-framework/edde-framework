<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Input;

	use Edde\Common\Html\AbstractHtmlControl;

	class PasswordControl extends AbstractHtmlControl {
		public function setValue(string $value) {
			$this->data('value', $value);
			return $this;
		}

		public function setPlaceholder(string $value) {
			$this->setAttribute('placeholder', $value);
			return $this;
		}

		protected function prepare() {
			parent::prepare()
				->javascript(self::class)
				->setTag('input', false)
				->addAttributeList([
					'type' => 'password',
					'value' => $this->getData('value', ''),
				]);
		}
	}
