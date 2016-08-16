<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;

	class SpanControl extends AbstractHtmlControl {
		public function setTag(string $tag, bool $pair = true) {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a span control.', $tag));
		}

		public function isPair() {
			return true;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('span');
		}
	}
