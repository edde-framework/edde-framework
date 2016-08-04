<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Html;

	class JavaScriptControl extends AbstractHtmlControl {
		public function getTag() {
			return 'script';
		}

		public function isPair() {
			return true;
		}

		public function setSrc($src) {
			$this->setAttribute('src', $src);
			return $this;
		}
	}
