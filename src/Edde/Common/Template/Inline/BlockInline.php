<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Common\Template\AbstractInline;

	/**
	 * Inline support for block macro.
	 */
	class BlockInline extends AbstractInline {
		public function __construct() {
			parent::__construct('t:block', true);
		}

		public function onMacro() {
			$this->compiler->block($this->attribute(null, false), [$this->macro]);
		}
	}
