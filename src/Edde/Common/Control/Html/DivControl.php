<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\HtmlException;

	class DivControl extends AbstractHtmlControl {
		public function setTag($tag, $pair = true) {
			throw new HtmlException(sprintf('Cannot set tag [%s] for a div control.', $tag));
		}

		public function getTag() {
			return 'div';
		}

		public function isPair() {
			return true;
		}

		protected function onPrepare() {
		}
	}
