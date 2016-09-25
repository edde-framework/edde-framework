<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class BlockMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:block', true);
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$compiler->block($this->attribute($macro, $compiler, 'id'), $macro->getNodeList());
		}
	}
