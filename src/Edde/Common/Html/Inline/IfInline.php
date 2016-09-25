<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;

	/**
	 * Condition support.
	 */
	class IfInline extends AbstractHtmlInline {
		/**
		 * Q: How many programmers does it take to change a light bulb?
		 *
		 * A: None, because it can't be done. It's a hardware problem.
		 */
		public function __construct() {
			parent::__construct('m:if', true);
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$macro->switch(new Node('if', null, ['src' => $this->extract($macro)]));
		}
	}
