<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractInline;

	class BlockInline extends AbstractInline {
		public function __construct() {
			parent::__construct('t:block');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$blockList = $compiler->getValue('block-list', []);
			if (isset($blockList[$id = $this->attribute($macro)])) {
				throw new MacroException(sprintf('Block id [%d] has been already defined.', $id));
			}
			$blockList[$id] = [$macro];
			$compiler->setValue('block-list', $blockList);
		}
	}
