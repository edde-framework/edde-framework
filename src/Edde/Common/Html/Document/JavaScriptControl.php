<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Script tag support.
	 */
	class JavaScriptControl extends AbstractHtmlControl {
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'script';
		}

		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return true;
		}

		public function setSrc(string $src) {
			$this->setAttribute('src', $src);
			return $this;
		}
	}
