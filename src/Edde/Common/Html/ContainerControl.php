<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlControl;

	class ContainerControl extends AbstractHtmlControl {
		public function render() {
			$this->use();
			/** @var $control IHtmlControl */
			foreach ($this->getControlList() as $control) {
				$control->render();
			}
		}
	}
