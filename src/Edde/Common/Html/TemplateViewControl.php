<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class TemplateViewControl extends ViewControl {
		use TemplateTrait;

		public function __call($name, $arguments) {
			if (strpos($name, 'action', 0) === false) {
				return parent::__call($name, $arguments);
			}
			$this->template();
			$this->send();
		}
	}
