<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

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
