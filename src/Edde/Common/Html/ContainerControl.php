<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlControl;

	class ContainerControl extends AbstractHtmlControl {
		public function render() {
			$this->use();
			$renderList = [];
			/** @var $control IHtmlControl */
			foreach ($this->getControlList() as $control) {
				$renderList[] = $control->render();
			}
			return implode('', $renderList);
		}
	}
