<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class BlockMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:block', true);
		}

		public function onMacro() {
			$blockList = $this->compiler->getVariable('block-list', []);
			if (isset($blockList[$id = $this->attribute($this->macro, 'id')])) {
				throw new MacroException(sprintf('Block id [%d] has been already defined.', $id));
			}
			$blockList[$id] = $this->macro->getNodeList();
			$this->compiler->setVariable('block-list', $blockList);
		}
	}
