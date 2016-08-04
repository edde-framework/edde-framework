<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class TitleControl extends AbstractHtmlControl {
		public function getTag() {
			return 'title';
		}

		public function isPair() {
			return true;
		}

		public function setTitle($title) {
			return $this->node->setValue($title);
		}
	}
