<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractInline;

	class BlockInline extends AbstractInline {
		public function __construct() {
			parent::__construct('t:block', true);
		}

		public function onMacro() {
			$blockList = $this->compiler->getVariable('block-list', []);
			if (isset($blockList[$id = $this->attribute($this->macro)])) {
				throw new MacroException(sprintf('Block id [%d] has been already defined.', $id));
			}
			$blockList[$id] = [$this->macro];
			$this->compiler->setVariable('block-list', $blockList);
			$this->macro->setMeta('id', $id);
		}
	}
