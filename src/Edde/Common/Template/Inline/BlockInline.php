<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
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
			parent::__construct('t:block');
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$compiler->block($this->attribute($macro, $compiler, null, false), [$macro]);
		}
	}
