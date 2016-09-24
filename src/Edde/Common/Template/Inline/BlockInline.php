<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Common\Template\AbstractInline;

	/**
	 * Inline support for block macro.
	 */
	class BlockInline extends AbstractInline {
		/**
		 * "The question of whether computers can think is just like the question of whether submarines can swim."
		 * - Edsger W. Dijkstra
		 */
		public function __construct() {
			parent::__construct('t:block', true);
		}

		public function onMacro() {
			$this->compiler->block($this->attribute(null, false), [$this->macro]);
		}
	}
