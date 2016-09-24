<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Control\IControl;
	use Edde\Api\Html\HtmlException;
	use Edde\Common\Html\AbstractHtmlControl;

	class ImgControl extends AbstractHtmlControl {
		public function getTag() {
			return 'img';
		}

		public function setSrc($src) {
			$this->setAttribute('src', $src);
			return $this;
		}

		public function isPair(): bool {
			return false;
		}

		public function addControl(IControl $control) {
			throw new HtmlException(sprintf('Cannot add control to an image control [%s].', static::class));
		}
	}
