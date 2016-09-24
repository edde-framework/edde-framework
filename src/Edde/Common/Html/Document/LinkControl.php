<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	class LinkControl extends AbstractHtmlControl {
		public function getTag() {
			return 'link';
		}

		public function isPair(): bool {
			return false;
		}
	}
