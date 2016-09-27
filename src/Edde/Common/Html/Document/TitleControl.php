<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	class TitleControl extends AbstractHtmlControl {
		public function getTag(): string {
			return 'title';
		}

		public function isPair(): bool {
			return true;
		}

		public function setTitle($title) {
			$this->use();
			return $this->node->setValue($title);
		}
	}
