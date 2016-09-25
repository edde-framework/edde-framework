<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;

	/**
	 * Support for inline loops.
	 */
	class LoopInline extends AbstractHtmlInline {
		/**
		 * Compaq is considering changing the command "Press Any Key" to "Press Return Key" because of the many calls asking where the "Any" key is.
		 */
		public function __construct() {
			parent::__construct('m:loop', true);
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$macro->switch(new Node('loop', null, ['src' => $this->extract($macro)]));
		}
	}
