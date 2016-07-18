<?php
	namespace Edde\Common\Control\Html;

	class TextInputControl extends AbstractHtmlControl {
		public function render() {
			$this->usse();
			return '<input type="text" value="' . $this->node->getValue() . '">';
		}

		protected function onPrepare() {
		}
	}
