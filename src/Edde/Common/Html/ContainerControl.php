<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class ContainerControl extends AbstractHtmlControl {
		public function render() {
			$this->use();
			foreach ($this->getControlList() as $control) {
				$control->render();
			}
		}
	}
