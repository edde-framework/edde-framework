<?php
	namespace Edde\Common\Control\Html;

	class LinkControl extends AbstractHtmlControl {
		public function getTag() {
			return 'link';
		}

		public function isPair() {
			return false;
		}

		protected function onPrepare() {
		}
	}
