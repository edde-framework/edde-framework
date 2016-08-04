<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Html;

	class BodyControl extends AbstractHtmlControl {
		public function getTag() {
			return 'body';
		}

		public function isPair() {
			return true;
		}
	}
