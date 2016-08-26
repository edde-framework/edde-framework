<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Control\IControl;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Html\AbstractHtmlControl;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class ImgControl extends AbstractHtmlControl {
		static public function macro(): IMacro {
			return new ControlMacro('img', static::class);
		}

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
