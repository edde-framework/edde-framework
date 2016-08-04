<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Html;

	class ContainerControl extends AbstractHtmlControl {
		public function render() {
			$this->usse();
			foreach ($this->getControlList() as $control) {
				$control->render();
			}
		}
	}
