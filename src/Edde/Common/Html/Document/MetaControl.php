<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	class MetaControl extends AbstractHtmlControl {
		public function getTag() {
			return 'meta';
		}

		public function isPair(): bool {
			return false;
		}
	}
