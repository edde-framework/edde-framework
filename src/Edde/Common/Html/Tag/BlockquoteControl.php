<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	class BlockquoteControl extends AbstractHtmlControl {
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a [%s] control.', $tag, static::class));
		}

		public function setCite(string $cite) {
			$this->setAttribute('cite', $cite);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('blockquote', true);
		}
	}
