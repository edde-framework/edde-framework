<?php
	namespace Edde\Common\Control\Html;

	class StyleSheetControl extends LinkControl {
		public function setHref($href) {
			$this->setAttribute('href', $href);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setAttribute('rel', 'stylesheet')
				->setAttribute('media', 'all');
		}
	}
