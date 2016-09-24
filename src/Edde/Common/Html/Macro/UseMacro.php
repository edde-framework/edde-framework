<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	/**
	 * Use macro can be used as a block reference "on demand" (similar to t:include macro).
	 */
	class UseMacro extends AbstractHtmlMacro {
		/**
		 * If God had intended Man to program, we would be born with USB ports.
		 */
		public function __construct() {
			parent::__construct('m:use', false);
		}

		protected function onMacro() {
			$this->write(sprintf('$this->block($stack->top(), %s);', ($helper = $this->compiler->helper($src = $this->attribute('src', false))) ? $helper : var_export($src, true)), 5);
		}
	}
