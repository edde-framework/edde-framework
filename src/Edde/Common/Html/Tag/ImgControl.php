<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Control\IControl;
	use Edde\Api\Html\HtmlException;
	use Edde\Common\Html\AbstractHtmlControl;

	class ImgControl extends AbstractHtmlControl {
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'img';
		}

		public function setSrc(string $src) {
			$this->setAttribute('src', $src);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return false;
		}

		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function addControl(IControl $control) {
			throw new HtmlException(sprintf('Cannot add control to an image control [%s].', static::class));
		}
	}
