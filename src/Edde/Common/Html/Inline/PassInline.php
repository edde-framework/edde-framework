<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Common\Node\Node;

	/**
	 * Pass macro is useful for transferring controls from templates back to a given control (root, super-root, ...).
	 */
	class PassInline extends AbstractHtmlInline {
		/**
		 * Hardware: The parts of a computer system that can be kicked.
		 */
		public function __construct() {
			parent::__construct('m:pass', true);
		}

		protected function onMacro() {
			$this->macro->prepend(new Node('pass', null, ['target' => $this->extract($this->macro, $this->getName(), null, false)]));
		}
	}
