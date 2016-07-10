<?php
	namespace Edde\Common\Control\Html;

	class MetaControl extends AbstractHtmlControl {
		public function getTag() {
			return 'meta';
		}

		public function isPair() {
			return false;
		}

		protected function onPrepare() {
		}
	}
