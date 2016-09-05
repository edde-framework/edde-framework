<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	class DivControl extends AbstractHtmlControl {
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a div control.', $tag));
		}

		public function isPair() {
			return true;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('div');
		}
	}
