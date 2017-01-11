<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Html table footer control.
	 */
	class TableFootControl extends AbstractHtmlControl {
		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a [%s] control.', $tag, static::class));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			parent::setTag('tfoot', true);
		}
	}
