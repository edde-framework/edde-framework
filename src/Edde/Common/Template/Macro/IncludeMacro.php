<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Compile time include macro.
	 */
	class IncludeMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:include');
		}

		public function macro(INode $node, IFile $source, ICompiler $compiler) {
		}
	}
