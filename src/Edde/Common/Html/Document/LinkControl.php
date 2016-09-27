<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	class LinkControl extends AbstractHtmlControl {
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'link';
		}

		public function isPair(): bool {
			return false;
		}
	}
