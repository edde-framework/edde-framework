<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\Html\HtmlException;
	use Edde\Api\Control\IControl;

	class ImgControl extends AbstractHtmlControl {
		public function getTag() {
			return 'img';
		}

		public function setSrc($src) {
			$this->setAttribute('src', $src);
			return $this;
		}

		public function isPair() {
			return false;
		}

		public function addControl(IControl $control) {
			throw new HtmlException(sprintf('Cannot add control to an image control [%s].', static::class));
		}
	}
