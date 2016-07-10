<?php
	namespace Edde\Common\Control\Html;

	class BodyControl extends AbstractHtmlControl {
		public function getTag() {
			return 'body';
		}

		public function isPair() {
			return true;
		}

		protected function onPrepare() {
		}
	}
