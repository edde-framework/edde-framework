<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	class BlockquoteControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingDocCommentInspection */
		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a [%s] control.', $tag, static::class));
		}

		/**
		 * set cite attribute of this blockquote
		 *
		 * @param string $cite
		 *
		 * @return $this
		 */
		public function setCite(string $cite) {
			$this->setAttribute('cite', $cite);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			parent::setTag('blockquote', true);
		}
	}
