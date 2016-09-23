<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Common\Template\AbstractMacro;

	class BlockMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:block', true);
		}

		public function onMacro() {
			$this->compiler->block($this->attribute('id'), $this->macro->getNodeList());
		}
	}
