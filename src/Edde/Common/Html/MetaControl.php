<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class MetaControl extends AbstractHtmlControl {
		public function getTag() {
			return 'meta';
		}

		public function isPair() {
			return false;
		}
	}
