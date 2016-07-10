<?php
	namespace Edde\Common\Control\Html;

	class StyleSheetControl extends LinkControl {
		public function setHref($href) {
			$this->setAttribute('href', $href);
			return $this;
		}

		protected function onPrepare() {
			$this->setAttribute('rel', 'stylesheet');
			$this->setAttribute('media', 'all');
		}
	}
