<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\ControlException;

	class TemplateViewControl extends ViewControl {
		use TemplateTrait;

		public function __call($name, $arguments) {
			if (strpos($name, 'action', 0) === false) {
				if (method_exists($this, $name) === false) {
					throw new ControlException(sprintf('Calling unknown method [%s::%s()].', static::class, $name));
				}
				$this->action();
				return parent::__call($name, $arguments);
			}
			$this->template();
			$this->response();
			return $this;
		}

		protected function action() {
		}
	}
