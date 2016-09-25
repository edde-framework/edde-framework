<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
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

		/**
		 * @inheritdoc
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$macro->prepend(new Node('pass', null, ['target' => $this->extract($macro)]));
		}
	}
