<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	class BodyControl extends AbstractHtmlControl {
		public function getTag() {
			return 'body';
		}

		public function isPair() {
			return true;
		}
	}
