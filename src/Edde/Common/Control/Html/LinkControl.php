<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Html;

	class LinkControl extends AbstractHtmlControl {
		public function getTag() {
			return 'link';
		}

		public function isPair() {
			return false;
		}
	}
