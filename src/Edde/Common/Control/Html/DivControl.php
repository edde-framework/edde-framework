<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\HtmlException;

	class DivControl extends AbstractHtmlControl {
		public function setTag($tag, $pair = true) {
			if ($tag !== null) {
				throw new HtmlException(sprintf('Cannot set tag [%s] for a div control.', $tag));
			}
			parent::setTag(null, false);
			return $this;
		}

		public function isPair() {
			return true;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('div');
		}

		protected function onPrepare() {
		}
	}
