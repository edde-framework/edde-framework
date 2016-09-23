<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	/**
	 * This macro enables code execution from control "in the middle of template" - for example delegated control creation moved on the
	 * control side.
	 */
	class CallMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct('call', false);
		}

		protected function onMacro() {
			$this->write($this->attribute('method') . ';', 5);
		}
	}
