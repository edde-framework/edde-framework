<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Control\IControl;
	use Edde\Api\Html\HtmlException;
	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Simple img html tag control.
	 */
	class ImgControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'img';
		}

		/**
		 * set img source (no transformations are applied, so src must be accessible from public)
		 *
		 * @param string $src
		 *
		 * @return ImgControl
		 */
		public function setSrc(string $src): ImgControl {
			$this->setAttribute('src', $src);
			return $this;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return false;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function addControl(IControl $control) {
			throw new HtmlException(sprintf('Cannot add control to an image control [%s].', static::class));
		}
	}
