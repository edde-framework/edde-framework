<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class BlockMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:block');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$id = $this->attribute($macro, 'id');
			$blockList = $compiler->getValue('block-list', []);
			if (isset($blockList[$id])) {
				throw new MacroException(sprintf('Block id [%d] has been already defined.', $id));
			}
			$blockList[$id] = $macro->getNodeList();
			$compiler->setValue('block-list', $blockList);
		}
	}
